<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();

    // available books for selector
    $books = fetchCashbookBooks($userId);

    // handle book selection form POST -> save in session and reload
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cashbookBookId'])) {
        $_SESSION['cashbookSelectedBook'] = intval($_POST['cashbookBookId']);
        setSuccessOrFailureMessage('success', 'Book Changed');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $book = findBookCashbook($userId);
    $bankAccounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $categories = $book ? fetchCategoriesFromCashbook($book) : [];

    // fetch all entries for the book
    $entries = $book ? executeQuery("SELECT * FROM cashbook_entry WHERE cashbook_book_id=" . intval($book) . " ORDER BY `date` DESC") : [];
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
            <div class="mt-3">
                <form id="cashbookFilterForm" class="row g-2 align-items-center">
                    <div class="col-auto">
                        <select name="time_range" class="form-select form-select-sm">
                            <option value="">All Time</option>
                            <option value="this_month">This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="this_year">This Year</option>
                            <option value="last_year">Last Year</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div class="col-auto custom-dates-wrapper" style="display:none;">
                        <input type="date" name="start_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto custom-dates-wrapper" style="display:none;">
                        <input type="date" name="end_date" class="form-control form-control-sm">
                    </div>
                    <div class="col-auto">
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">All Categories</option>
                            <?php foreach($categories as $id=>$n): ?>
                            <option value="<?php echo $id;?>"><?php echo htmlspecialchars($n); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="bank_account_id" class="form-select form-select-sm">
                            <option value="">All Accounts</option>
                            <?php foreach($bankAccounts as $a): ?>
                            <option value="<?php echo $a['id'];?>"><?php echo htmlspecialchars($a['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                        <button id="clearCashbookFiltersBtn" class="btn btn-sm btn-outline-secondary ms-1" type="button">Clear</button>
                    </div>
                </form>
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
    const CURRENT_BOOK = <?php echo json_encode($book); ?>;
</script>

<!-- Client-side filtering & pagination -->
<script>
    (function () {
        const STORAGE_KEY = 'cashbookFilters_' + CURRENT_BOOK;
        const ITEMS_PER_PAGE_KEY = 'cashbookItemsPerPage_' + CURRENT_BOOK;

        const form = document.getElementById('cashbookFilterForm');
        const entriesContainer = document.getElementById('cashbookEntries');
        const pagination = document.getElementById('cashbookPagination');
        const perPageSelect = document.getElementById('cashbookItemsPerPage');

        if (!form || !entriesContainer || !pagination || !perPageSelect) return;

        // Restore filter state
        let filterState = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
        if (form.time_range) form.time_range.value = filterState.time_range || '';
        if (form.start_date) form.start_date.value = filterState.start_date || '';
        if (form.end_date) form.end_date.value = filterState.end_date || '';
        if (form.category_id) form.category_id.value = filterState.category_id || '';
        if (form.bank_account_id) form.bank_account_id.value = filterState.bank_account_id || '';

        function updateCustomDates() {
            const isCustom = form.time_range.value === 'custom';
            document.querySelectorAll('.custom-dates-wrapper')
                .forEach(el => el.style.display = isCustom ? 'block' : 'none');
        }
        form.time_range.addEventListener('change', updateCustomDates);
        updateCustomDates();

        let currentPage = 1;
        let itemsPerPage = parseInt(localStorage.getItem(ITEMS_PER_PAGE_KEY) || perPageSelect.value);
        perPageSelect.value = itemsPerPage;

        function filterEntries() {
            const f = {
                time_range: form.time_range.value,
                start_date: form.start_date.value,
                end_date: form.end_date.value,
                category_id: form.category_id.value,
                bank_account_id: form.bank_account_id.value
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

            switch (f.time_range) {
                case 'this_month':
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0); // last day of this month
                    break;

                case 'last_month':
                    start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                    end = new Date(today.getFullYear(), today.getMonth(), 0); // last day of last month
                    break;

                case 'this_year':
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today.getFullYear(), 11, 31);
                    break;

                case 'last_year':
                    const y = today.getFullYear() - 1;
                    start = new Date(y, 0, 1);
                    end = new Date(y, 11, 31);
                    break;

                case 'custom':
                    start = f.start_date ? new Date(f.start_date) : null;
                    end = f.end_date ? new Date(f.end_date) : null;
                    break;
            }
            
            console.log(start, end);

            if (start) start = normalizeStart(start);
            if (end) end = normalizeEnd(end);

            let filtered = CASHBOOK_ENTRIES.filter(e => {
                const entryDate = new Date(e.date.replace(' ', 'T'));

                if (start && entryDate < start) return false;
                if (end && entryDate > end) return false;

                if (f.category_id && Number(f.category_id) !== Number(e.category_id)) return false;
                if (f.bank_account_id && Number(f.bank_account_id) !== Number(e.bank_account_id)) return false;

                return true;
            });

            renderEntries(filtered);
            renderTotals(filtered);
            renderPagination(filtered);
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
                                    <div class="text-muted small"><i class="bi bi-calendar-event me-1"></i>${e.date}</div>
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
                            ${e.creation_timestamp?`<div class="mt-3 small text-muted">Created: ${e.creation_timestamp}</div>`:''}
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
            const income = entries.filter(e=>['income','transfer_in','lend_repayment'].includes(e.type)).reduce((a,b)=>a+parseFloat(b.amount),0);
            const expense = entries.filter(e=>['expense','transfer_out','lend','repayment'].includes(e.type)).reduce((a,b)=>a+parseFloat(b.amount),0);
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

        form.addEventListener('submit', e=>{e.preventDefault(); currentPage=1; filterEntries();});
        document.getElementById('clearCashbookFiltersBtn').addEventListener('click', e=>{
            e.preventDefault(); localStorage.removeItem(STORAGE_KEY); form.reset(); updateCustomDates(); currentPage=1; filterEntries();
        });
        perPageSelect.addEventListener('change', function(){itemsPerPage=parseInt(this.value); localStorage.setItem(ITEMS_PER_PAGE_KEY,itemsPerPage); currentPage=1; filterEntries();});

        filterEntries();
    })();
</script>

<?php initializePageFooter($rootPath, $moduleType); ?>
