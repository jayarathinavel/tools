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
                        <a class="btn btn-sm btn-info text-white" href="books/view.php">
                            <i class="bi bi-book me-1"></i>Books
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

                        <div class="mb-3">
                            <label class="form-label small">Time range</label>
                            <select name="time_range"
                                    class="form-select form-select-sm"
                                    id="timeRangeSelect"></select>
                        </div>

                        <div class="mb-3 custom-dates-wrapper" style="display:none;">
                            <label class="form-label small">Start date</label>
                            <input type="date"
                                name="start_date"
                                class="form-control form-control-sm">
                        </div>

                        <div class="mb-3 custom-dates-wrapper" style="display:none;">
                            <label class="form-label small">End date</label>
                            <input type="date"
                                name="end_date"
                                class="form-control form-control-sm">
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Category</label>
                            <select name="category_id"
                                    class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $id=>$n): ?>
                                    <option value="<?= $id ?>"><?= htmlspecialchars($n) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Account</label>
                            <select name="bank_account_id"
                                    class="form-select form-select-sm">
                                <option value="">All Accounts</option>
                                <?php foreach($bankAccounts as $a): ?>
                                    <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small">Type</label>
                            <select name="type"
                                    class="form-select form-select-sm">
                                <option value="">All Types</option>
                                <option value="income">Income</option>
                                <option value="expense">Expense</option>
                                <option value="transfer_in">Transfer In</option>
                                <option value="transfer_out">Transfer Out</option>
                                <option value="lend">Lend</option>
                                <option value="repayment">Repayment</option>
                                <option value="lend_repayment">Lend Repayment</option>
                                <option value="investment">Investment</option>
                            </select>
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
                        <div class="small text-muted">Total Income</div>
                        <div class="fw-bold total-income">0.00</div>
                    </td>
                    <td class="bg-light text-danger border">
                        <div class="small text-muted">Total Expenses</div>
                        <div class="fw-bold total-expense">0.00</div>
                    </td>
                    <td class="bg-white border">
                        <div class="small text-muted">Net</div>
                        <div class="fw-bold total-net">0.00</div>
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
            if (!timeRangeSelect || !CASHBOOK_ENTRIES.length) return;

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

            timeRangeSelect.innerHTML = '';

            const addOption = (value, label, parent) => {
                const opt = document.createElement('option');
                opt.value = value;
                opt.textContent = label;
                (parent || timeRangeSelect).appendChild(opt);
            };

            // Always
            addOption('', 'All Time');

            // ⏱ Show month shortcuts ONLY if current year exists
            if (hasCurrentYear) {
                addOption('this_month', 'This Month');
                addOption('last_month', 'Last Month');
                addOption('next_month', 'Next Month');
            }

            // Year shortcuts only when meaningful
            if (years.length > 1) {
                addOption('this_year', 'This Year');
                addOption('last_year', 'Last Year');
            }

            // Month groups for every year
            years.forEach(y => {
                const group = document.createElement('optgroup');
                group.label = y.toString();
                timeRangeSelect.appendChild(group);

                [...yearMonthMap[y]]
                    .sort((a, b) => a - b)
                    .forEach(m => {
                        addOption(
                            `month_${y}_${m}`,
                            monthNames[m],
                            group
                        );
                    });
            });

            addOption('custom', 'Custom');
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

        function renderEntries(entries){
            entriesContainer.innerHTML = '';
            const start = (currentPage-1)*itemsPerPage;
            const end = start+itemsPerPage;
            entries.slice(start,end).forEach(e=>{
                const catName = e.category_id ? (CATEGORIES[e.category_id]||'') : '';
                const accName = e.bank_account_id ? (ACCOUNTS[e.bank_account_id]?.name||'') : '';
                const amountClass = ['income','transfer_in','lend_repayment'].includes(e.type) ? 'text-success':'text-danger';
                const html = `
                <div class="col-12 col-md-6 col-lg-4 mb-2">
                    <div class="card mb-2 shadow-sm h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1 text-break">${e.title}</h5>
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
                case 'repayment': return '⟵ '+parseFloat(e.amount).toFixed(2);
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
            const netEl = document.querySelector('.total-net');
            netEl.innerText=(income-expense).toFixed(2);
            netEl.className='fw-bold total-net '+(income-expense>=0?'text-success':'text-danger');
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
                summaryEl.style.display = 'none';
                summaryEl.innerHTML = '';
            }
        }

        form.search.addEventListener('input', () => {
            currentPage = 1;
            filterEntries();
        });

        form.addEventListener('submit', e=>{e.preventDefault(); currentPage=1; filterEntries();});
        clearCashbookFiltersBtn.addEventListener('click', e=>{
            e.preventDefault(); localStorage.removeItem(STORAGE_KEY); form.reset(); updateCustomDates(); currentPage=1; filterEntries();
            clearCashbookFiltersBtn.style.display = 'none';
        });
        perPageSelect.addEventListener('change', function(){itemsPerPage=parseInt(this.value); localStorage.setItem(ITEMS_PER_PAGE_KEY,itemsPerPage); currentPage=1; filterEntries();});

        filterEntries();
    })();
</script>

<?php initializePageFooter($rootPath, $moduleType); ?>
