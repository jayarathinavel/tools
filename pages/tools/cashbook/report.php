<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook Report", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    $bankAccounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $categories = $book ? fetchCategoriesFromCashbook($book) : [];

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

                    <fieldset class="btn-group">
                        <legend class="visually-hidden">Select Book</legend>
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
                    </fieldset>
                </div>
            
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                    <!-- Buttons Section + Book selector dropdown -->
                    <div class="d-flex flex-wrap gap-2 ms-md-3 align-items-center">

                        <a class="btn btn-sm btn-info text-white" href="view.php">
                            <i class="bi bi-eye me-1"></i>View Book
                        </a>

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
                    style="max-width:220px;" hidden> <!-- hidden so form.search is not null -->
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

            <!-- Reports Section -->
            <div class="row mt-4">
                <!-- Report by Category -->
                <div id="reportCategoryContainer" class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="bi bi-tags me-2"></i>By Category</h6>
                        </div>
                        <div class="card-body">
                            <div id="reportCategory" class="report-content">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report by Type -->
                <div id="reportTypeContainer" class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0"><i class="bi bi-diagram-3 me-2"></i>By Type</h6>
                        </div>
                        <div class="card-body">
                            <div id="reportType" class="report-content">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report by Account -->
                <div id="reportAccountContainer" class="col-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="bi bi-bank me-2"></i>By Account</h6>
                        </div>
                        <div class="card-body">
                            <div id="reportAccount" class="report-content">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Report by Month -->
                <div id="reportMonthContainer" class="col-12 mb-3">
                    <div class="card shadow-sm">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="bi bi-calendar-month me-2"></i>By Month</h6>
                        </div>
                        <div class="card-body">
                            <div id="reportMonth" class="report-content">
                                <div class="d-flex justify-content-center align-items-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

<!-- Report generation script -->
<script>
    (function () {
        const STORAGE_KEY = 'cashbookFilters_' + CURRENT_USER;
        const form = document.getElementById('cashbookFilterForm');
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

        function buildTimeRangeOptions() {
            if (!timeRangeSelect || !CASHBOOK_ENTRIES.length) return;

            const today = new Date();
            const currentYear = today.getFullYear();

            const monthNames = [
                'January','February','March','April','May','June',
                'July','August','September','October','November','December'
            ];

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

            addOption('', 'All Time');

            if (hasCurrentYear) {
                addOption('this_month', 'This Month');
                addOption('last_month', 'Last Month');
                addOption('next_month', 'Next Month');
            }

            if (years.length > 1) {
                addOption('this_year', 'This Year');
                addOption('last_year', 'Last Year');
            }

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

        function getFilteredEntries() {
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

            return { filtered, filters: f };
        }

        function renderReports() {
            const { filtered, filters } = getFilteredEntries();

            renderFilterSummary(filters);
            renderCategoryReport(filtered, filters);
            renderTypeReport(filtered, filters);
            renderAccountReport(filtered, filters);
            renderMonthReport(filtered, filters);

            if (Object.values(filters).some(v => v)) {
                clearCashbookFiltersBtn.style.display = 'inline-block';
            } else {
                clearCashbookFiltersBtn.style.display = 'none';
            }
        }

        function renderFilterSummary(f) {
            const parts = [];

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

            if (f.category_id) {
                parts.push(`Category: ${CATEGORIES[f.category_id]}`);
            }

            if (f.bank_account_id) {
                parts.push(`Account: ${ACCOUNTS[f.bank_account_id]?.name}`);
            }

            if (f.type) {
                parts.push(`Type: ${f.type.replace('_', ' ')}`);
            }

            if (f.search) {
                parts.push(`Search: "${f.search}"`);
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

        function renderAccountReport(entries, filters) {
            const container = document.getElementById('reportAccountContainer');
            const report = document.getElementById('reportAccount');

            // Hide if account filter is applied
            if (filters.bank_account_id) {
                container.style.display = 'none';
                return;
            }
            container.style.display = '';

            const accountData = {};
            entries.forEach(e => {
                const accId = e.bank_account_id || 'uncategorized';
                const accName = e.bank_account_id ? (ACCOUNTS[e.bank_account_id]?.name || 'Unknown') : 'No Account';
                if (!accountData[accId]) {
                    accountData[accId] = { name: accName, total: 0, income: 0, expense: 0, count: 0 };
                }
                const amount = parseFloat(e.amount);
                const investmentAmount = parseFloat(e.amount);
                if (['income', 'lend_repayment'].includes(e.type)) {
                    accountData[accId].income += amount;
                    accountData[accId].total += amount;
                }
                if(['investment'].includes(e.type)) {
                    accountData[accId].investment += investmentAmount;
                } else if (['expense', 'lend'].includes(e.type)) {
                    accountData[accId].expense += amount;
                    accountData[accId].total -= amount;
                }
                accountData[accId].count++;
            });

            let html = '';
            Object.values(accountData).forEach(data => {
                const balanceClass = data.total >= 0 ? 'text-success' : 'text-danger';
                html += `
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">${data.name}</h6>
                                <small class="text-muted">${data.count} transaction(s)</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold ${balanceClass}">${data.total.toFixed(2)}</div>
                                <small class="text-success">+${data.income.toFixed(2)}</small>
                                <small class="text-danger ms-2">-${data.expense.toFixed(2)}</small>
                            </div>
                        </div>
                    </div>
                `;
            });

            if (!html) html = '<p class="text-muted">No data</p>';
            report.innerHTML = html;
        }

        function renderCategoryReport(entries, filters) {
            const container = document.getElementById('reportCategoryContainer');
            const report = document.getElementById('reportCategory');

            // Hide if category filter is applied
            if (filters.category_id) {
                container.style.display = 'none';
                return;
            }
            container.style.display = '';

            const categoryData = {};
            entries.forEach(e => {
                const catId = e.category_id || 'uncategorized';
                const catName = e.category_id ? (CATEGORIES[e.category_id] || 'Unknown') : 'Uncategorized';
                if (!categoryData[catId]) {
                    categoryData[catId] = { name: catName, total: 0, count: 0 };
                }
                const amount = parseFloat(e.amount);
                categoryData[catId].total += amount;
                categoryData[catId].count++;
            });

            // Sort by total amount
            const sorted = Object.values(categoryData).sort((a, b) => Math.abs(b.total) - Math.abs(a.total));

            let html = '';
            sorted.forEach(data => {
                html += `
                    <div class="mb-2 pb-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>${data.name}</small>
                                <div class="text-muted small">${data.count} item(s)</div>
                            </div>
                            <div class="text-end fw-bold text-primary">
                                ${data.total.toFixed(2)}
                            </div>
                        </div>
                    </div>
                `;
            });

            if (!html) html = '<p class="text-muted">No data</p>';
            report.innerHTML = html;
        }

        function renderTypeReport(entries, filters) {
            const container = document.getElementById('reportTypeContainer');
            const report = document.getElementById('reportType');

            // Hide if type filter is applied
            if (filters.type) {
                container.style.display = 'none';
                return;
            }
            container.style.display = '';

            const typeData = {};
            entries.forEach(e => {
                if (!typeData[e.type]) {
                    typeData[e.type] = { total: 0, count: 0 };
                }
                const amount = parseFloat(e.amount);
                typeData[e.type].total += amount;
                typeData[e.type].count++;
            });

            const typeLabels = {
                'income': 'Income',
                'expense': 'Expense',
                'transfer_in': 'Transfer In',
                'transfer_out': 'Transfer Out',
                'lend': 'Lend',
                'lend_repayment': 'Lend Repayment',
                'investment': 'Investment'
            };

            const typeClass = {
                'income': 'text-success',
                'expense': 'text-danger',
                'transfer_in': 'text-success',
                'transfer_out': 'text-danger',
                'lend': 'text-danger',
                'lend_repayment': 'text-success',
                'investment': 'text-success'
            };

            let html = '';
            Object.keys(typeData).sort().forEach(type => {
                const data = typeData[type];
                const label = typeLabels[type] || type;
                const amountClass = typeClass[type] || '';
                html += `
                    <div class="mb-2 pb-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>${label}</small>
                                <div class="text-muted small">${data.count} item(s)</div>
                            </div>
                            <div class="text-end fw-bold ${amountClass}">
                                ${data.total.toFixed(2)}
                            </div>
                        </div>
                    </div>
                `;
            });

            if(typeData['investment'] && typeData['expense']){
                html += `<div class="mb-2 pb-2 border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small>Expenses and Investments</small>
                            </div>
                            <div class="text-end fw-bold text-primary">
                                ${
                                    (
                                        Number(typeData['investment'].total) +
                                        Number(typeData['expense'].total)
                                    ).toFixed(2)
                                }
                            </div>
                        </div>
                    </div>`;
            }
            
            if (!html) html = '<p class="text-muted">No data</p>';
            report.innerHTML = html;
        }

        function renderMonthReport(entries, filters) {
            const container = document.getElementById('reportMonthContainer');
            const report = document.getElementById('reportMonth');

            // Hide if time filter is already applied (since we're showing month-wise breakdown)
            if (filters.time_range && filters.time_range !== '' && filters.time_range !== 'custom' && filters.time_range !== 'this_month' && !filters.time_range.startsWith('month_')) {
                container.style.display = 'none';
                return;
            }
            container.style.display = '';

            const monthData = {};
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            entries.forEach(e => {
                const d = new Date(e.date.replace(' ', 'T'));
                const year = d.getFullYear();
                const month = d.getMonth();
                const monthKey = `${year}-${month}`;
                const monthLabel = `${monthNames[month]} ${year}`;

                if (!monthData[monthKey]) {
                    monthData[monthKey] = { label: monthLabel, total: 0, income: 0, expense: 0, in: 0, out: 0, lend: 0, lend_repayment: 0, count: 0, investment: 0, expensesAndInvestments: 0 };
                }

                const amount = parseFloat(e.amount);
                const investmentAmount = parseFloat(e.amount);
                if (['income', 'lend_repayment'].includes(e.type)) {
                    monthData[monthKey].in += amount;
                    monthData[monthKey].total += amount;
                }
                if(['investment'].includes(e.type)) {
                    monthData[monthKey].investment += investmentAmount;
                    monthData[monthKey].expensesAndInvestments += investmentAmount;
                }
                if(['income'].includes(e.type)) {
                    monthData[monthKey].income += amount;
                }
                if(['expense'].includes(e.type)) {
                    monthData[monthKey].expense += amount;
                    monthData[monthKey].expensesAndInvestments += amount;
                }
                if (['lend'].includes(e.type)) {
                    monthData[monthKey].lend += amount;
                }
                if (['lend_repayment'].includes(e.type)) {
                    monthData[monthKey].lend_repayment += amount;
                }
                if (['expense', 'lend', 'investment'].includes(e.type)) {
                    monthData[monthKey].out += amount;
                    monthData[monthKey].total -= amount;
                }
                monthData[monthKey].count++;
            });

            // Sort by date descending
            const sorted = Object.keys(monthData).sort((a, b) => {
                const [ya, ma] = a.split('-').map(Number);
                const [yb, mb] = b.split('-').map(Number);
                if (ya !== yb) return yb - ya;
                return mb - ma;
            }).map(k => monthData[k]);

            let html = '<div class="list-group">';

            sorted.forEach(data => {
                const netClass = data.total >= 0 ? 'text-success' : 'text-danger';

                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between mb-1">
                            <strong>${data.label}</strong>
                            <span class="fw-bold ${netClass}">
                                ${data.total.toFixed(2)}
                            </span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>Income</span>
                            <span class="text-success">${data.income.toFixed(2)}</span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>Lend Repayment</span>
                            <span class="text-success">${data.lend_repayment.toFixed(2)}</span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>Expenses</span>
                            <span class="text-danger">${data.expense.toFixed(2)}</span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>Lend</span>
                            <span class="text-danger">${data.lend.toFixed(2)}</span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>Investments</span>
                            <span style="color: blue;">${data.investment.toFixed(2)}</span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>Expenses + Investments</span>
                            <span style="color: green">${data.expensesAndInvestments.toFixed(2)}</span>
                        </div>

                        <div class="small d-flex justify-content-between">
                            <span>In / Out</span>
                            <span>
                                <span class="text-success">+${data.in.toFixed(2)}</span>
                                /
                                <span class="text-danger">-${data.out.toFixed(2)}</span>
                            </span>
                        </div>
                    </div>
                `;
            });

            html += '</div>';


            if (sorted.length === 0) html = '<p class="text-muted">No data</p>';
            report.innerHTML = html;
        }

        // Event listeners for filter changes
        form.addEventListener('submit', e => {
            e.preventDefault();
            renderReports();
        });

        form.search.addEventListener('input', () => {
            renderReports();
        });

        form.time_range.addEventListener('change', () => {
            renderReports();
        });

        form.category_id.addEventListener('change', () => {
            renderReports();
        });

        form.bank_account_id.addEventListener('change', () => {
            renderReports();
        });

        form.type.addEventListener('change', () => {
            renderReports();
        });

        // Clear filters
        const clearCashbookFiltersBtn = document.getElementById('clearCashbookFiltersBtn');
        if (clearCashbookFiltersBtn) {
            clearCashbookFiltersBtn.addEventListener('click', e => {
                e.preventDefault();
                localStorage.removeItem(STORAGE_KEY);
                form.reset();
                updateCustomDates();
                renderReports();
            });
        }

        // Initial render
        renderReports();
    })();
</script>

<?php initializePageFooter($rootPath, $moduleType); ?>