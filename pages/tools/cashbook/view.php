<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();

    // available books for selector
    $books = fetchCashbookBooks($userId);

    // handle book selection form POST -> save in session, persist and reload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cashbookBookId'])) {
        $bookId = intval($_POST['cashbookBookId']);
        $_SESSION['cashbookSelectedBook'] = $bookId;
        if (isset($_SESSION['appUserId'])) {
            setUserDefaultBook('cashbook', $bookId);
        }
        setSuccessOrFailureMessage('success', 'Book Changed');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $book = findBookCashbook($userId);
    $book ? canViewBook($book) : null;
    $bankAccounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $categories = $book ? fetchCategoriesFromCashbook($book) : [];

    // fetch all entries for the book
    $entries = $book ? executeQuery("SELECT * FROM cashbook_entry WHERE cashbook_book_id=" . intval($book) . " ORDER BY `date` DESC, `id` DESC") : [];
    $entriesArr = [];
    if($entries) {
        while($e = mysqli_fetch_assoc($entries)) {
            $entriesArr[] = $e;
        }
    } else {
        $entriesArr = [];
    }
    
?>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>

    <?php if($book): ?>
    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <!-- Book Actions -->
            <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                <div class="d-flex align-items-center gap-3">
                    <!-- Title with dropdown -->
                    <style>
                        /* make split button look like a heading and hide text while open */
                        .title-btn { font-size: 1.5rem; font-weight: 700; color: inherit; text-decoration: none; }
                        .btn-group.show .title-text { visibility: hidden; }
                        .title-btn:focus, .title-split-toggle:focus { box-shadow: none; }

                        /* Filter pill buttons styling */
                        .category-pill, .account-pill, .type-pill {
                            font-size: 0.85rem;
                            font-weight: 500;
                            border-radius: 20px;
                            padding: 0.4rem 0.8rem;
                            transition: all 0.2s ease;
                            white-space: nowrap;
                        }

                        .category-pill.active, .account-pill.active, .type-pill.active {
                            font-weight: 600;
                            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                        }

                        .category-pill:hover, .account-pill:hover, .type-pill:hover {
                            transform: translateY(-1px);
                            box-shadow: 0 3px 6px rgba(0,0,0,0.12);
                        }

                        .category-filter-wrapper, .account-filter-wrapper, .type-filter-wrapper {
                            max-height: 200px;
                            overflow-y: auto;
                            padding: 0.25rem;
                        }

                        .offcanvas-body {
                            padding: 1.5rem;
                        }

                        .btn-outline-purple {
                            color: #6f42c1;
                            border-color: #6f42c1;
                        }

                        .btn-outline-purple:hover {
                            color: #fff;
                            background-color: #6f42c1;
                            border-color: #6f42c1;
                        }

                        .btn-purple {
                            color: #fff;
                            background-color: #6f42c1;
                            border-color: #6f42c1;
                        }

                        .btn-purple:hover {
                            background-color: #5a32a3;
                            border-color: #5a32a3;
                        }

                        .description-modal-body {
                            white-space: pre-line;
                        }
                    </style>

                    <div class="btn-group" role="group">
                        <!-- main title button -->
                        <button type="button" class="btn btn-link title-btn m-0 p-0">
                            <span class="title-text"><?php echo ($book && isset($books[$book])) ? htmlspecialchars($books[$book]) : 'Change Book'; ?></span>
                        </button>
            
                        <!-- split dropdown toggle -->
                        <button type="button" class="btn btn-link dropdown-toggle dropdown-toggle-split title-split-toggle p-0 ms-2"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="visually-hidden">Toggle Book List</span>
                        </button>
            
                        <ul class="dropdown-menu">
                            <?php foreach ($books as $id => $name): ?>
                                <li>
                                    <form method="post" class="m-0">
                                        <input type="hidden" name="cashbookBookId" value="<?php echo $id; ?>">
                                        <button type="submit"
                                            class="dropdown-item <?php echo ($book && intval($book) === intval($id)) ? 'active' : ''; ?>">
                                            <?php echo htmlspecialchars($name); ?>
                                        </button>
                                    </form>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                    <!-- Buttons Section + Book selector dropdown -->
                    <div class="d-flex flex-wrap gap-2 ms-md-3 align-items-center">
                        <a class="btn btn-sm btn-primary" href="add.php">
                            <i class="bi bi-plus-lg me-1"></i>Add Entry
                        </a>
                        <a class="btn btn-sm btn-secondary" href="summary.php">
                            <i class="bi bi-list-task me-1"></i>Summary
                        </a>
                        <a class="btn btn-sm btn-info" href="report.php">
                            <i class="bi-graph-up"></i>
                        </a>
                        <a class="btn btn-sm btn-info text-white" href="books/view.php">
                            <i class="bi bi-book"></i>
                        </a>
                        <a class="btn btn-sm btn-primary" title="View Accounts" href="banks/view.php">
                            <i class="bi bi-bank"></i>
                        </a>
                        <a class="btn btn-sm btn-secondary" title="View Categories" href="categories/view.php">
                            <i class="bi bi-tags"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="d-flex flex-wrap gap-2 align-items-center mt-3">

                <!-- 🔍 Search -->
                <input type="search"
                    name="search"
                    form="cashbookFilterForm"
                    class="form-control form-control-sm"
                    placeholder="Search entries…"
                    style="max-width:220px;">

                <!-- 🎛️ Open offcanvas -->
                <button class="btn btn-sm btn-outline-secondary ms-auto"
                        type="button"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#cashbookFiltersCanvas">
                    <i class="bi bi-sliders"></i> Filters
                </button>

                <!-- 🧹 Clear -->
                <button id="clearCashbookFiltersBtn"
                        class="btn btn-sm btn-outline-warning"
                        type="button"
                        style="display:none;">
                    Clear
                </button>
            </div>

            <form id="cashbookFilterForm">
                <div class="offcanvas offcanvas-end"
                    tabindex="-1"
                    id="cashbookFiltersCanvas">

                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">
                            <i class="bi bi-funnel-fill me-1"></i>Filters
                        </h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="offcanvas"></button>
                    </div>

                    <div class="offcanvas-body">

                        <!-- Time Range -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-calendar3 me-1"></i>Time Range
                            </label>
                            <div class="time-range-wrapper d-flex flex-wrap gap-2" id="timeRangeWrapper">
                                <button type="button" class="btn btn-sm btn-outline-secondary time-range-pill active" data-time-range="">
                                    All Time
                                </button>
                            </div>
                            <input type="hidden" name="time_range" value="">
                        </div>

                        <!-- Custom Date Range -->
                        <div class="mb-4 custom-dates-wrapper" style="display:none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">Start date</label>
                                    <input type="date"
                                        name="start_date"
                                        class="form-control form-control-sm">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">End date</label>
                                    <input type="date"
                                        name="end_date"
                                        class="form-control form-control-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-tag me-1"></i>Category
                            </label>
                            <div class="category-filter-wrapper d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary category-pill active" data-category-id="">
                                    All
                                </button>
                                <?php foreach($categories as $id=>$n): ?>
                                    <button type="button" class="btn btn-sm btn-outline-primary category-pill" data-category-id="<?= $id ?>" title="<?= htmlspecialchars($n) ?>">
                                        <?= htmlspecialchars($n) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="category_id" value="">
                        </div>

                        <!-- Account Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-bank me-1"></i>Account
                            </label>
                            <div class="account-filter-wrapper d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary account-pill active" data-account-id="">
                                    All
                                </button>
                                <?php foreach($bankAccounts as $a): ?>
                                    <button type="button" class="btn btn-sm btn-outline-info account-pill" data-account-id="<?= $a['id'] ?>" title="<?= htmlspecialchars($a['name']) ?>">
                                        <?= htmlspecialchars($a['name']) ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="bank_account_id" value="">
                        </div>

                        <!-- Type Filter -->
                        <div class="mb-4">
                            <label class="form-label fw-semibold mb-2">
                                <i class="bi bi-arrows-move me-1"></i>Transaction Type
                            </label>
                            <div class="type-filter-wrapper d-flex flex-wrap gap-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary type-pill active" data-type="">
                                    All
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success type-pill" data-type="income" title="Income">
                                    <i class="bi bi-arrow-down-circle me-1"></i>Income
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger type-pill" data-type="expense" title="Expense">
                                    <i class="bi bi-arrow-up-circle me-1"></i>Expense
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-info type-pill" data-type="transfer_in" title="Transfer In">
                                    <i class="bi bi-arrow-left-circle me-1"></i>Transfer In
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-warning type-pill" data-type="transfer_out" title="Transfer Out">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Transfer Out
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-purple type-pill" data-type="lend" title="Lend">
                                    <i class="bi bi-hand-index me-1"></i>Lend
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-purple type-pill" data-type="lend_repayment" title="Lend Repayment">
                                    <i class="bi bi-hand-thumbs-up me-1"></i>Repayment
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary type-pill" data-type="investment" title="Investment">
                                    <i class="bi bi-graph-up-arrow me-1"></i>Investment
                                </button>
                            </div>
                            <input type="hidden" name="type" value="">
                        </div>

                    </div>

                    <div class="offcanvas-footer border-top p-3 d-flex gap-2">
                        <button class="btn btn-primary btn-sm flex-fill"
                                type="submit"
                                data-bs-dismiss="offcanvas">
                            Apply
                        </button>

                        <button class="btn btn-outline-secondary btn-sm flex-fill"
                                type="button"
                                onclick="document.getElementById('clearCashbookFiltersBtn').click()">
                            Clear
                        </button>
                    </div>
                </div>
            </form>

            <div id="cashbookFilterSummary"
                class="small text-muted mt-2"
                style="display:none;">
            </div>


            <!-- Totals -->
            <table class="table table-borderless text-center mt-3">
                <tr>
                    <td class="bg-light text-success border">
                        <div class="small text-muted">Income</div>
                        <div class="fw-bold total-income">0.00</div>
                    </td>
                    <td class="bg-light text-danger border">
                        <div class="small text-muted">Expenses</div>
                        <div class="fw-bold total-expense">0.00</div>
                    </td>
                </tr>
            </table>

            <!-- Entries & pagination -->
            <div class="row" id="cashbookEntries"></div>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <small class="text-muted">
                    Show
                    <select id="cashbookItemsPerPage" class="form-select form-select-sm d-inline-block w-auto mx-1">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                    entries per page
                </small>
            </div>
            <div class="d-flex justify-content-center mt-3">
                <nav>
                    <ul id="cashbookPagination" class="pagination pagination-sm mb-0"></ul>
                </nav>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="alert alert-warning">No book available. <a href="books/add.php">Create one</a> first. </div>
    <?php endif; ?>
</div>

<!-- Pass entries and metadata to JS -->
<script>
    const CASHBOOK_ENTRIES = <?php echo json_encode($entriesArr, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;
    const CATEGORIES = <?php echo json_encode($categories); ?>;
    const ACCOUNTS = <?php echo json_encode(array_column($bankAccounts, null, 'id')); ?>;
    const CURRENT_USER = <?php echo json_encode($userId); ?>;
</script>

<!-- Client-side filtering & pagination -->
<script>
    (function () {
        const STORAGE_KEY = 'cashbookFilters_' + CURRENT_USER;
        const ITEMS_PER_PAGE_KEY = 'cashbookItemsPerPage_' + CURRENT_USER;

        const form = document.getElementById('cashbookFilterForm');
        const entriesContainer = document.getElementById('cashbookEntries');
        const pagination = document.getElementById('cashbookPagination');
        const perPageSelect = document.getElementById('cashbookItemsPerPage');
        const clearCashbookFiltersBtn = document.getElementById('clearCashbookFiltersBtn');


        if (!form || !entriesContainer || !pagination || !perPageSelect) return;

        const timeRangeSelect = document.getElementById('timeRangeSelect');
        const timeRangeWrapper = document.getElementById('timeRangeWrapper');

        buildTimeRangeOptions();

        // Restore filter state
        let filterState = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        if (form.search) form.search.value = filterState.search || '';
        if (form.time_range) form.time_range.value = filterState.time_range || '';
        if (form.start_date) form.start_date.value = filterState.start_date || '';
        if (form.end_date) form.end_date.value = filterState.end_date || '';
        if (form.category_id) form.category_id.value = filterState.category_id || '';
        if (form.bank_account_id) form.bank_account_id.value = filterState.bank_account_id || '';
        if (form.type) form.type.value = filterState.type || '';

        function updateCustomDates() {
            const isCustom = form.time_range.value === 'custom';

            document.querySelectorAll('.custom-dates-wrapper')
                .forEach(el => el.style.display = isCustom ? 'block' : 'none');

            if (!isCustom) return;

            const bounds = getEntryDateBounds();
            if (!bounds) return;

            const end = new Date(bounds.max);
            const start = new Date(end);
            start.setDate(start.getDate() - 50);

            // Clamp start to earliest entry
            if (start < bounds.min) {
                start.setTime(bounds.min.getTime());
            }

            form.end_date.value = end.toISOString().slice(0, 10);
            form.start_date.value = start.toISOString().slice(0, 10);
        }

        function getEntryDateBounds() {
            if (!CASHBOOK_ENTRIES.length) return null;

            let min = null;
            let max = null;

            CASHBOOK_ENTRIES.forEach(e => {
                const d = new Date(e.date.replace(' ', 'T'));
                if (!min || d < min) min = d;
                if (!max || d > max) max = d;
            });

            return { min, max };
        }

        form.time_range.addEventListener('change', updateCustomDates);

        let currentPage = 1;
        let itemsPerPage = parseInt(localStorage.getItem(ITEMS_PER_PAGE_KEY) || perPageSelect.value);
        perPageSelect.value = itemsPerPage;

        function buildTimeRangeOptions() {
            if (!CASHBOOK_ENTRIES.length) return;

            const today = new Date();
            const currentYear = today.getFullYear();

            const monthNames = [
                'January','February','March','April','May','June',
                'July','August','September','October','November','December'
            ];

            // Build year → months map
            const yearMonthMap = {};
            CASHBOOK_ENTRIES.forEach(e => {
                const d = new Date(e.date.replace(' ', 'T'));
                const y = d.getFullYear();
                const m = d.getMonth();

                if (!yearMonthMap[y]) yearMonthMap[y] = new Set();
                yearMonthMap[y].add(m);
            });

            const years = Object.keys(yearMonthMap)
                .map(Number)
                .sort((a, b) => b - a);

            const hasCurrentYear = years.includes(currentYear);

            // Clear wrapper and rebuild with pill buttons
            if (timeRangeWrapper) {
                timeRangeWrapper.innerHTML = '';

                // Always show "All Time"
                const addButton = (value, label) => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.className = 'btn btn-sm btn-outline-secondary time-range-pill';
                    if (value === '') btn.classList.add('active', 'btn-secondary');
                    btn.setAttribute('data-time-range', value);
                    btn.textContent = label;
                    timeRangeWrapper.appendChild(btn);
                };

                addButton('', 'All Time');

                // ⏱ Show month shortcuts ONLY if current year exists
                if (hasCurrentYear) {
                    addButton('this_month', 'This Month');
                    addButton('last_month', 'Last Month');
                    addButton('next_month', 'Next Month');
                }

                // Year shortcuts only when meaningful
                if (years.length > 1) {
                    addButton('this_year', 'This Year');
                    addButton('last_year', 'Last Year');
                }

                // Month groups for every year
                years.forEach(y => {
                    [...yearMonthMap[y]]
                        .sort((a, b) => a - b)
                        .forEach(m => {
                            addButton(`month_${y}_${m}`, `${monthNames[m]} ${y}`);
                        });
                });

                addButton('custom', 'Custom');

                // Attach event listeners to all time range pills
                document.querySelectorAll('.time-range-pill').forEach(btn => {
                    btn.addEventListener('click', e => {
                        e.preventDefault();
                        document.querySelectorAll('.time-range-pill').forEach(b => b.classList.remove('active', 'btn-secondary'));
                        document.querySelectorAll('.time-range-pill').forEach(b => b.classList.add('btn-outline-secondary'));
                        btn.classList.remove('btn-outline-secondary');
                        btn.classList.add('active', 'btn-secondary');
                        form.time_range.value = btn.getAttribute('data-time-range');
                        updateCustomDates();
                        currentPage = 1;
                        filterEntries();
                    });
                });
            }
        }

        function updateFilterButtonState(f) {
            const btn = document.getElementById('cashbookApplyFilterBtn');
            if (!btn) return;

            const hasFilters = Object.values(f).some(v => v);

            btn.classList.remove('btn-primary', 'btn-success', 'btn-warning');

            if (hasFilters) {
                btn.classList.add('btn-success'); // 👈 active state
            } else {
                btn.classList.add('btn-primary'); // 👈 default
            }
        }


        function filterEntries() {
            const f = {
                time_range: form.time_range.value,
                start_date: form.start_date.value,
                end_date: form.end_date.value,
                category_id: form.category_id.value,
                bank_account_id: form.bank_account_id.value,
                search: form.search.value.trim().toLowerCase(),
                type: form.type.value
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(f));

            function normalizeStart(date) {
                date.setHours(0, 0, 0, 0);
                return date;
            }

            function normalizeEnd(date) {
                date.setHours(23, 59, 59, 999);
                return date;
            }


            const today = new Date();
            let start = null, end = null;

            switch (true) {
                case f.time_range === 'this_month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;

                case f.time_range === 'last_month':
                    start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    end = new Date(today.getFullYear(), today.getMonth(), 0);
                    break;

                case f.time_range === 'next_month':
                    start = new Date(today.getFullYear(), today.getMonth() + 1, 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 2, 0);
                    break;

                case f.time_range === 'this_year':
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today.getFullYear(), 11, 31);
                    break;

                case f.time_range === 'last_year':
                    start = new Date(today.getFullYear() - 1, 0, 1);
                    end = new Date(today.getFullYear() - 1, 11, 31);
                    break;

                case f.time_range?.startsWith('month_'): {
                    const [, y, m] = f.time_range.split('_').map(Number);
                    start = new Date(y, m, 1);
                    end = new Date(y, m + 1, 0);
                    break;
                }

                case f.time_range === 'custom':
                    start = f.start_date ? new Date(f.start_date) : null;
                    end = f.end_date ? new Date(f.end_date) : null;
                    break;
            }

            
            if (start) start = normalizeStart(start);
            if (end) end = normalizeEnd(end);

            let filtered = CASHBOOK_ENTRIES.filter(e => {
                const entryDate = new Date(e.date.replace(' ', 'T'));

                if (start && entryDate < start) return false;
                if (end && entryDate > end) return false;

                if (f.category_id && Number(f.category_id) !== Number(e.category_id)) return false;
                if (f.bank_account_id && Number(f.bank_account_id) !== Number(e.bank_account_id)) return false;
                if (f.type && e.type !== f.type) return false;

                // 🔍 SEARCH MATCH
                if (f.search) {
                    const catName = e.category_id ? (CATEGORIES[e.category_id] || '') : '';
                    const accName = e.bank_account_id ? (ACCOUNTS[e.bank_account_id]?.name || '') : '';

                    const haystack = [
                        e.title,
                        e.type,
                        e.amount,
                        catName,
                        accName
                    ].join(' ').toLowerCase();

                    if (!haystack.includes(f.search)) return false;
                }

                return true;
            });

            renderEntries(filtered);
            renderTotals(filtered);
            renderPagination(filtered);
            renderFilterSummary(f);

            if (Object.values(f).some(v => v)) {
                clearCashbookFiltersBtn.style.display = 'inline-block';
            } else {
                clearCashbookFiltersBtn.style.display = 'none';
            }
        }

        function truncateText(text, limit){
            if(text.length <= limit) return text;
            return text.substring(0, limit) + '...';
        }

        function escapeHtml(text){
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function renderEntries(entries){
            entriesContainer.innerHTML = '';
            const start = (currentPage-1)*itemsPerPage;
            const end = start+itemsPerPage;
            entries.slice(start,end).forEach(e=>{
                const catName = e.category_id ? (CATEGORIES[e.category_id]||'') : '';
                const accName = e.bank_account_id ? (ACCOUNTS[e.bank_account_id]?.name||'') : '';
                const amountClass = ['income','transfer_in','lend_repayment'].includes(e.type) ? 'text-success':'text-danger';
                const truncatedDescriptionLength = 35;
                const html = `
                <div class="col-12 col-md-6 col-lg-4 mb-2">
                    <div class="card mb-2 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-break">${e.title}</h5>
                                    ${e.description ? `
                                        <p class="card-text small text-muted mb-1 description-preview">
                                            ${truncateText(e.description, truncatedDescriptionLength)}
                                            ${e.description.length > truncatedDescriptionLength ? `
                                                <button class="btn btn-link btn-sm p-0 ms-1 show-description"
                                                    data-title="${escapeHtml(e.title)}"
                                                    data-description="${escapeHtml(e.description)}">
                                                    more
                                                </button>` : ''}
                                        </p>
                                    ` : ''}
                                    <div class="small"><i class="bi bi-calendar-event me-1"></i>${e.date}</div>
                                    <div class="mt-2">${catName?`<span class="badge bg-primary me-1">${catName}</span>`:''}${accName?`<span class="badge bg-info">${accName}</span>`:''}</div>
                                </div>
                                <div class="text-end">
                                    <div class="fs-5 fw-bold ${amountClass}">${formatAmount(e)}</div>
                                    <small class="text-muted">${e.type.replace('_',' ')}</small>
                                    <div class="mt-2">
                                        <a class="btn btn-sm btn-outline-warning me-1" href="edit.php?id=${e.id}" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                                        <a class="btn btn-sm btn-outline-danger" href="delete.php?id=${e.id}&operation=delete" onclick="return confirm('Delete this entry?');" title="Delete"><i class="bi bi-trash-fill"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`;
                entriesContainer.insertAdjacentHTML('beforeend', html);
            });
        }

        function formatAmount(e){
            switch(e.type){
                case 'income': return '+'+parseFloat(e.amount).toFixed(2);
                case 'expense': return '-'+parseFloat(e.amount).toFixed(2);
                case 'lend': return '→ '+parseFloat(e.amount).toFixed(2);
                case 'lend_repayment': return '⟶ '+parseFloat(e.amount).toFixed(2);
                case 'transfer_in': return '⇒ '+parseFloat(e.amount).toFixed(2);
                case 'transfer_out': return '⇐ '+parseFloat(e.amount).toFixed(2);
                case 'investment': return '⤴ '+parseFloat(e.amount).toFixed(2);
                default: return parseFloat(e.amount).toFixed(2);
            }
        }

        function renderTotals(entries){
            const income = entries.filter(e=>['income'].includes(e.type)).reduce((a,b)=>a+parseFloat(b.amount),0);
            const expense = entries.filter(e=>['expense'].includes(e.type)).reduce((a,b)=>a+parseFloat(b.amount),0);
            document.querySelector('.total-income').innerText='+'+income.toFixed(2);
            document.querySelector('.total-expense').innerText='-'+expense.toFixed(2);
        }

        function renderPagination(filtered) {
            totalItems = filtered.length;
            pagination.innerHTML = '';

            const totalPages = Math.ceil(totalItems / itemsPerPage);
            if (totalPages <= 1) {
                pagination.style.display = 'none';
                return;
            }
            pagination.style.display = '';

            const maxVisible = 5; // current ±2
            const pages = [];

            const push = p => pages.push(p);

            // Always include first page
            push(1);

            let start = Math.max(2, currentPage - 2);
            let end = Math.min(totalPages - 1, currentPage + 2);

            // Insert ellipsis if needed
            if (start > 2) push('...');
            for (let i = start; i <= end; i++) push(i);
            if (end < totalPages - 1) push('...');

            // Always include last page
            if (totalPages > 1) push(totalPages);

            const makeBtn = (label, page, disabled, active) => {
                const li = document.createElement('li');
                li.className =
                    'page-item' +
                    (disabled ? ' disabled' : '') +
                    (active ? ' active' : '');

                const a = document.createElement('a');
                a.href = '#';
                a.className = 'page-link';
                a.textContent = label;

                if (typeof page === 'number') {
                    a.onclick = e => {
                        e.preventDefault();
                        if (!disabled && !active) {
                            currentPage = page;
                            filterEntries();
                        }
                    };
                }

                li.appendChild(a);
                return li;
            };

            // ⏮ First
            pagination.appendChild(
                makeBtn('«', 1, currentPage === 1)
            );

            // ◀ Prev
            pagination.appendChild(
                makeBtn('‹', currentPage - 1, currentPage === 1)
            );

            // Pages
            pages.forEach(p => {
                if (p === '...') {
                    const li = document.createElement('li');
                    li.className = 'page-item disabled';
                    li.innerHTML = `<span class="page-link">…</span>`;
                    pagination.appendChild(li);
                } else {
                    pagination.appendChild(
                        makeBtn(p, p, false, p === currentPage)
                    );
                }
            });

            // ▶ Next
            pagination.appendChild(
                makeBtn('›', currentPage + 1, currentPage === totalPages)
            );

            // ⏭ Last
            pagination.appendChild(
                makeBtn('»', totalPages, currentPage === totalPages)
            );
        }

        function renderFilterSummary(f) {
            const parts = [];

            // Time range
            if (f.time_range) {
                const map = {
                    this_month: 'This month',
                    last_month: 'Last month',
                    next_month: 'Next month',
                    this_year: 'This year',
                    last_year: 'Last year'
                };

                if (map[f.time_range]) {
                    parts.push(map[f.time_range]);
                } else if (f.time_range.startsWith('month_')) {
                    const [, y, m] = f.time_range.split('_');
                    const month = new Date(y, m).toLocaleString('default', { month: 'long' });
                    parts.push(`${month} ${y}`);
                } else if (f.time_range === 'custom') {
                    if (f.start_date && f.end_date) {
                        parts.push(`${f.start_date} → ${f.end_date}`);
                    } else if (f.start_date) {
                        parts.push(`From ${f.start_date}`);
                    } else if (f.end_date) {
                        parts.push(`Until ${f.end_date}`);
                    }
                }
            }

            // Category
            if (f.category_id) {
                parts.push(`Category: ${CATEGORIES[f.category_id]}`);
            }

            // Account
            if (f.bank_account_id) {
                parts.push(`Account: ${ACCOUNTS[f.bank_account_id]?.name}`);
            }

            // Type
            if (f.type) {
                parts.push(`Type: ${f.type.replace('_', ' ')}`);
            }

            // Search
            if (f.search) {
                parts.push(`Search: “${f.search}”`);
            }

            const summaryEl = document.getElementById('cashbookFilterSummary');

            if (parts.length) {
                summaryEl.innerHTML =
                    `<i class="bi bi-info-circle me-1"></i>
                    <strong>Applied filters:</strong> ${parts.join(' · ')}`;
                summaryEl.style.display = '';
            } else {
                summaryEl.innerHTML = '';
                // Check if 'this_month' button exists in time range pills
                const thisMonthBtn = document.querySelector('.time-range-pill[data-time-range="this_month"]');
                if (thisMonthBtn) {
                    summaryEl.innerHTML = `
                        <a href="#" class="text-decoration-none" id="filterThisMonthLink">
                            <i class="bi bi-funnel me-1"></i>Current month
                        </a>
                    `;
                    summaryEl.style.display = '';
                    
                    // Add click handler
                    document.getElementById('filterThisMonthLink').addEventListener('click', e => {
                        e.preventDefault();
                        thisMonthBtn.click();
                    });
                } else {
                    summaryEl.style.display = 'none';
                }

            }
        }

        // Category pill buttons
        document.querySelectorAll('.category-pill').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('.category-pill').forEach(b => b.classList.remove('active', 'btn-primary'));
                document.querySelectorAll('.category-pill').forEach(b => b.classList.add('btn-outline-primary'));
                btn.classList.remove('btn-outline-primary');
                btn.classList.add('active', 'btn-primary');
                form.category_id.value = btn.getAttribute('data-category-id');
                currentPage = 1;
                filterEntries();
            });
        });

        // Account pill buttons
        document.querySelectorAll('.account-pill').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                document.querySelectorAll('.account-pill').forEach(b => b.classList.remove('active', 'btn-info'));
                document.querySelectorAll('.account-pill').forEach(b => b.classList.add('btn-outline-info'));
                btn.classList.remove('btn-outline-info');
                btn.classList.add('active', 'btn-info');
                form.bank_account_id.value = btn.getAttribute('data-account-id');
                currentPage = 1;
                filterEntries();
            });
        });

        // Type pill buttons
        document.querySelectorAll('.type-pill').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                // Remove all active and filled button classes from all pills
                document.querySelectorAll('.type-pill').forEach(b => {
                    b.classList.remove('active', 'btn-success', 'btn-danger', 'btn-info', 'btn-warning', 'btn-purple', 'btn-secondary');
                });
                // Reset all pills to outline style
                document.querySelectorAll('.type-pill').forEach(b => {
                    const type = b.getAttribute('data-type');
                    if (type === 'income') b.classList.add('btn-outline-success');
                    else if (type === 'expense') b.classList.add('btn-outline-danger');
                    else if (type === 'transfer_in') b.classList.add('btn-outline-info');
                    else if (type === 'transfer_out') b.classList.add('btn-outline-warning');
                    else if (['lend', 'lend_repayment'].includes(type)) b.classList.add('btn-outline-purple');
                    else if (type === 'investment') b.classList.add('btn-outline-secondary');
                    else b.classList.add('btn-outline-secondary');
                });
                
                // Apply active state to clicked button
                const btnClass = btn.getAttribute('data-type') === 'income' ? 'btn-success' :
                               btn.getAttribute('data-type') === 'expense' ? 'btn-danger' :
                               btn.getAttribute('data-type') === 'transfer_in' ? 'btn-info' :
                               btn.getAttribute('data-type') === 'transfer_out' ? 'btn-warning' :
                               ['lend', 'lend_repayment'].includes(btn.getAttribute('data-type')) ? 'btn-purple' :
                               btn.getAttribute('data-type') === 'investment' ? 'btn-secondary' :
                               'btn-secondary';
                
                btn.classList.add('active', btnClass);
                const outline = btnClass.replace('btn-', 'btn-outline-');
                btn.classList.remove(outline);
                form.type.value = btn.getAttribute('data-type');
                currentPage = 1;
                filterEntries();
            });
        });

        form.search.addEventListener('input', () => {
            currentPage = 1;
            filterEntries();
        });

        form.addEventListener('submit', e=>{e.preventDefault(); currentPage=1; filterEntries();});
        clearCashbookFiltersBtn.addEventListener('click', e=>{
            e.preventDefault(); 
            
            // Select all time range (first button - All Time)
            const allTimeBtn = document.querySelector('.time-range-pill[data-time-range=""]');
            if (allTimeBtn) allTimeBtn.click();
            
            // Select all categories (All button)
            const allCategoryBtn = document.querySelector('.category-pill[data-category-id=""]');
            if (allCategoryBtn) allCategoryBtn.click();
            
            // Select all accounts (All button)
            const allAccountBtn = document.querySelector('.account-pill[data-account-id=""]');
            if (allAccountBtn) allAccountBtn.click();
            
            // Select all types (All button)
            const allTypeBtn = document.querySelector('.type-pill[data-type=""]');
            if (allTypeBtn) allTypeBtn.click();
            
            // Clear search
            form.search.value = '';
            
            localStorage.removeItem(STORAGE_KEY);
            currentPage = 1;
            filterEntries();
            clearCashbookFiltersBtn.style.display = 'none';
        });
        perPageSelect.addEventListener('change', function(){itemsPerPage=parseInt(this.value); localStorage.setItem(ITEMS_PER_PAGE_KEY,itemsPerPage); currentPage=1; filterEntries();});

        // Initialize pill buttons based on stored filter state
        if (filterState.time_range) {
            const timeRangeBtn = document.querySelector(`.time-range-pill[data-time-range="${filterState.time_range}"]`);
            if (timeRangeBtn) timeRangeBtn.click();
        }
        if (filterState.category_id) {
            const categoryBtn = document.querySelector(`.category-pill[data-category-id="${filterState.category_id}"]`);
            if (categoryBtn) categoryBtn.click();
        }
        if (filterState.bank_account_id) {
            const accountBtn = document.querySelector(`.account-pill[data-account-id="${filterState.bank_account_id}"]`);
            if (accountBtn) accountBtn.click();
        }
        if (filterState.type) {
            const typeBtn = document.querySelector(`.type-pill[data-type="${filterState.type}"]`);
            if (typeBtn) typeBtn.click();
        }

        filterEntries();

        entriesContainer.querySelectorAll('.show-description').forEach(btn=>{
            btn.addEventListener('click', function(){
                const modal = document.getElementById('descriptionModal');

                modal.querySelector('.modal-title').textContent = this.dataset.title;
                modal.querySelector('.modal-body').textContent = this.dataset.description;

                const bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            });
        });
    })();
</script>

<div class="modal fade" id="descriptionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body small text-muted description-modal-body"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<?php initializePageFooter($rootPath, $moduleType); ?>
