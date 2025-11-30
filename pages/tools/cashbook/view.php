<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();

    // available books for selector
    $books = fetchCashbookBooks($userId);

    // handle book selection form POST -> save in session and reload to avoid resubmit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cashbookBookId'])) {
        $_SESSION['cashbookSelectedBook'] = intval($_POST['cashbookBookId']);
        setSuccessOrFailureMessage('success', 'Book Changed');
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $book = findBookCashbook($userId);
    $bankAccounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $categories = $book ? fetchCategoriesFromCashbook($book) : [];

    // preserve filter selections
    $selectedTimeRange = '';
    $selectedStartDate = '';
    $selectedEndDate = '';
    $selectedCategoryId = '';
    $selectedBankAccountId = '';

    // build filter form values and where clause
    $where = [];
    $start = null;
    $end = null;

    if(isset($_POST['filter']) && $book) {
        $f = $_POST;

        // read posted values first
        $selectedTimeRange = isset($f['time_range']) ? $f['time_range'] : '';
        $selectedStartDate = isset($f['start_date']) ? $f['start_date'] : '';
        $selectedEndDate = isset($f['end_date']) ? $f['end_date'] : '';
        $selectedCategoryId = isset($f['category_id']) ? $f['category_id'] : '';
        $selectedBankAccountId = isset($f['bank_account_id']) ? $f['bank_account_id'] : '';

        // determine start/end from presets or direct dates
        if(!empty($selectedTimeRange)){
            $today = date('Y-m-d');
            switch($selectedTimeRange){
                case 'this_month':
                    $start = date('Y-m-01');
                    $end = $today;
                    break;
                case 'last_month':
                    $start = date('Y-m-01', strtotime('first day of last month'));
                    $end = date('Y-m-t', strtotime('last month'));
                    break;
                case 'this_year':
                    $start = date('Y-01-01');
                    $end = $today;
                    break;
                case 'last_year':
                    $y = date('Y') - 1;
                    $start = "$y-01-01";
                    $end = "$y-12-31";
                    break;
                case 'custom':
                    // if custom, prefer explicit posted dates
                    if (!empty($selectedStartDate) && !empty($selectedEndDate)) {
                        $start = $selectedStartDate;
                        $end = $selectedEndDate;
                    }
                    break;
            }
        }

        // if user provided start/end dates without selecting preset, use them
        if ((empty($start) || empty($end)) && !empty($selectedStartDate) && !empty($selectedEndDate)) {
            $start = $selectedStartDate;
            $end = $selectedEndDate;
            // reflect this in UI as custom
            if ($selectedTimeRange === '') $selectedTimeRange = 'custom';
        }

        if(!empty($start) && !empty($end)) {
            $where[] = "`date` BETWEEN '" . mysqli_real_escape_string(Database::getInstance()->getConnection(), $start) . "' AND '" . mysqli_real_escape_string(Database::getInstance()->getConnection(), $end) . "'";
            // update UI date inputs to show computed values for presets
            $selectedStartDate = $start;
            $selectedEndDate = $end;
        }

        if(!empty($f['category_id'])) $where[] = "category_id=" . intval($f['category_id']);
        if(!empty($f['bank_account_id'])) $where[] = "bank_account_id=" . intval($f['bank_account_id']);
    }

    $where[] = "cashbook_book_id=" . intval($book);
    $whereSql = $book ? ('WHERE ' . implode(' AND ', $where)) : '';
    $entries = $book ? executeQuery("SELECT * FROM cashbook_entry $whereSql ORDER BY `date` DESC") : [];

    // compute totals for current filter (uses same $whereSql as entries query)
    $totals = ['income' => 0.0, 'expense' => 0.0];
    if ($book) {
        $totRes = executeQuery("SELECT `type`, IFNULL(SUM(amount),0) AS sum FROM cashbook_entry $whereSql GROUP BY `type`");
        while ($r = mysqli_fetch_assoc($totRes)) {
            $type = $r['type'];
            $totals[$type] = floatval($r['sum']);
        }
    }
?>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>

    <?php if($book): ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <!-- Book selector -->
                <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <?php if ($book && isset($books[$book])): ?>
                            <h2><strong><?php echo htmlspecialchars($books[$book]); ?></strong></h2>
                        <?php endif; ?>
                        <!-- Toggle button -->
                        <button title="Change Book" type="button" id="toggleBookSelector" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <form id="bookSelectorForm" method="post" class="d-none m-0">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-white border-end-0">Change Book</span>
                                <select name="cashbookBookId" title="Select Book" onchange="this.form.submit()" class="form-select form-select-sm">
                                    <option value="" disabled <?php echo empty($book) ? 'selected' : ''; ?>>Select book</option>
                                    <?php foreach ($books as $id => $name): ?>
                                        <option value="<?php echo $id; ?>" <?php echo ($book && intval($book) === intval($id)) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </form>
                    </div>

                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center gap-3">
                        <!-- Buttons Section -->
                        <div class="d-flex flex-wrap gap-2 ms-md-3">
                            <a class="btn btn-sm btn-primary" href="add.php">
                                <i class="bi bi-plus-lg me-1"></i>Add Entry
                            </a>
                            <a class="btn btn-sm btn-secondary" href="summary.php">
                                <i class="bi bi-list-task me-1"></i>Summary
                            </a>
                            <a class="btn btn-sm btn-info text-white" href="books/view.php">
                                <i class="bi bi-book me-1"></i>Manage Books
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="mt-3">
                    <form id="cashbookFilterForm" method="post" class="row g-2 align-items-center">
                        <input type="hidden" name="filter" value="1">

                        <div class="col-auto">
                            <select name="time_range" class="form-select form-select-sm">
                                <option value="">All Time</option>
                                <option value="this_month" <?php echo ($selectedTimeRange === 'this_month') ? 'selected' : ''; ?>>This Month</option>
                                <option value="last_month" <?php echo ($selectedTimeRange === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                                <option value="this_year" <?php echo ($selectedTimeRange === 'this_year') ? 'selected' : ''; ?>>This Year</option>
                                <option value="last_year" <?php echo ($selectedTimeRange === 'last_year') ? 'selected' : ''; ?>>Last Year</option>
                                <option value="custom" <?php echo ($selectedTimeRange === 'custom') ? 'selected' : ''; ?>>Custom</option>
                            </select>
                        </div>

                        <div class="col-auto custom-dates-wrapper" style="display:<?php echo ($selectedTimeRange === 'custom') ? 'block' : 'none'; ?>;">
                            <input type="date" name="start_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($selectedStartDate); ?>">
                        </div>
                        <div class="col-auto custom-dates-wrapper" style="display:<?php echo ($selectedTimeRange === 'custom') ? 'block' : 'none'; ?>;">
                            <input type="date" name="end_date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($selectedEndDate); ?>">
                        </div>

                        <div class="col-auto">
                            <select name="category_id" class="form-select form-select-sm">
                                <option value="">All Categories</option>
                                <?php foreach($categories as $id=>$n): ?>
                                    <option value="<?php echo $id;?>" <?php echo ($selectedCategoryId !== '' && intval($selectedCategoryId) === intval($id)) ? 'selected' : ''; ?>><?php echo htmlspecialchars($n); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-auto">
                            <select name="bank_account_id" class="form-select form-select-sm">
                                <option value="">All Accounts</option>
                                <?php foreach($bankAccounts as $a): ?>
                                    <option value="<?php echo $a['id'];?>" <?php echo ($selectedBankAccountId !== '' && intval($selectedBankAccountId) === intval($a['id'])) ? 'selected' : ''; ?>><?php echo htmlspecialchars($a['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-auto">
                            <button class="btn btn-sm btn-primary" type="submit"><i class="bi bi-funnel-fill me-1"></i>Filter</button>
                            <button id="clearCashbookFiltersBtn" class="btn btn-sm btn-outline-secondary ms-1" type="button"
                                style="<?php echo (isset($_POST['filter']) || (!empty($selectedTimeRange) || !empty($selectedStartDate) || !empty($selectedCategoryId) || !empty($selectedBankAccountId))) ? 'display:inline-block;' : 'display:none;'; ?>">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Totals Section -->
                <table class="table table-borderless text-center mt-3">
                    <tr>
                        <td class="bg-light text-success border">
                            <div class="small text-muted">Total Income</div>
                            <div class="fs-5 fw-bold">
                                +<?php echo number_format($totals['income'], 2); ?>
                            </div>
                        </td>
                        <td class="bg-light text-danger border">
                            <div class="small text-muted">Total Expenses</div>
                            <div class="fs-5 fw-bold">
                                -<?php echo number_format($totals['expense'], 2); ?>
                            </div>
                        </td>
                        <td class="bg-white border">
                            <div class="small text-muted">Net</div>
                            <div class="fs-5 fw-bold 
                                <?php echo ($totals['income']-$totals['expense']>=0)?'text-success':'text-danger'; ?>">
                                <?php echo number_format($totals['income'] - $totals['expense'], 2); ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <script>
            (function(){
                const form = document.getElementById('cashbookFilterForm');
                if (!form) return;
                const storageKey = 'cashbookFilters_v1';
                const autoFlag = 'cashbookFiltersAutoApplied';

                // Restore saved filters
                try {
                    const saved = localStorage.getItem(storageKey);
                    const clearBtn = document.getElementById('clearCashbookFiltersBtn');
                    if (saved) {
                        if (clearBtn) clearBtn.style.display = 'inline-block';
                        const data = JSON.parse(saved);
                        if (data.time_range !== undefined && form.time_range) form.time_range.value = data.time_range;
                        if (data.start_date !== undefined && form.start_date) form.start_date.value = data.start_date;
                        if (data.end_date !== undefined && form.end_date) form.end_date.value = data.end_date;
                        if (data.category_id !== undefined && form.category_id) form.category_id.value = data.category_id;
                        if (data.bank_account_id !== undefined && form.bank_account_id) form.bank_account_id.value = data.bank_account_id;

                        // Auto-submit only if server hasn't already applied the filter and not auto-applied in this session
                        var serverApplied = <?php echo isset($_POST['filter']) ? 'true' : 'false'; ?>;
                        if (!serverApplied && sessionStorage.getItem(autoFlag) !== '1') {
                            sessionStorage.setItem(autoFlag, '1');
                            form.submit();
                        }
                    } else {
                        // Hide button if no filters saved
                        if (clearBtn) clearBtn.style.display = 'none';
                    }
                } catch(e) {
                    console.warn('Failed to restore cashbook filters', e);
                }

                // Save current filters on submit
                form.addEventListener('submit', function(){
                    try {
                        const data = {
                            time_range: form.time_range ? form.time_range.value : '',
                            start_date: form.start_date ? form.start_date.value : '',
                            end_date: form.end_date ? form.end_date.value : '',
                            category_id: form.category_id ? form.category_id.value : '',
                            bank_account_id: form.bank_account_id ? form.bank_account_id.value : ''
                        };
                        localStorage.setItem(storageKey, JSON.stringify(data));
                    } catch(e) { console.warn('Failed to save cashbook filters', e); }
                });

                // Clear saved filters button
                const clearBtn = document.getElementById('clearCashbookFiltersBtn');
                if (clearBtn) {
                    clearBtn.addEventListener('click', function(e){
                        e.preventDefault();
                        try {
                            localStorage.removeItem(storageKey);
                            sessionStorage.removeItem(autoFlag);
                        } catch(e) {}
                        if (clearBtn) clearBtn.style.display = 'none';
                        // reset UI values
                        if (form.time_range) form.time_range.value = '';
                        if (form.start_date) form.start_date.value = '';
                        if (form.end_date) form.end_date.value = '';
                        if (form.category_id) form.category_id.value = '';
                        if (form.bank_account_id) form.bank_account_id.value = '';
                        form.submit();
                    });
                }
            
                // Show/hide custom date fields
                function updateCustomDateVisibility() {
                    const isCustom = form.time_range && form.time_range.value === 'custom';
                    document.querySelectorAll('.custom-dates-wrapper').forEach(el => {
                        el.style.display = isCustom ? 'block' : 'none';
                    });
                }

                // Listen for time range changes
                if (form.time_range) {
                    form.time_range.addEventListener('change', updateCustomDateVisibility);
                }

                // Apply visibility on page load (including restored values)
                updateCustomDateVisibility();

            
            })();

            document.getElementById('toggleBookSelector').addEventListener('click', function () {
                document.getElementById('bookSelectorForm').classList.toggle('d-none');
            });
        </script>

        <div class="row">
            <?php while($e = mysqli_fetch_assoc($entries)):
                $catName = ($e['category_id'] && isset($categories[$e['category_id']])) ? $categories[$e['category_id']] : '';
                $accName = ($e['bank_account_id'] && isset($bankAccounts[$e['bank_account_id']]['name'])) ? $bankAccounts[$e['bank_account_id']]['name'] : '';
                $amountClass = ($e['type'] === 'income') ? 'text-success' : 'text-danger';
            ?>
            <div class="col-12 col-md-6 col-lg-4 mb-2">
                <div class="card mb-2 shadow-sm h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($e['title']); ?></h5>
                                <div class="text-muted small">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    <?php echo htmlspecialchars($e['date']); ?>
                                </div>
                                <div class="mt-2">
                                    <?php if ($catName): ?>
                                        <span class="badge bg-primary me-1"><?php echo htmlspecialchars($catName); ?></span>
                                    <?php endif; ?>
                                    <?php if ($accName): ?>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($accName); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="text-end">
                                <div class="fs-5 fw-bold <?php echo $amountClass; ?>">
                                    <?php echo ($e['type'] === 'income') ? '+' : '-'; ?><?php echo number_format($e['amount'], 2); ?>
                                </div>
                                <div class="mt-2">
                                    <a class="btn btn-sm btn-outline-warning me-1" href="edit.php?id=<?php echo $e['id']; ?>" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?php echo $e['id']; ?>&operation=delete" onclick="return confirm('Delete this entry?');" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($e['creation_timestamp'])): ?>
                            <div class="mt-3 small text-muted">Created: <?php echo htmlspecialchars($e['creation_timestamp']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-warning">No book selected / available. <a href="books/add.php">Create one</a> first. </div>
    <?php endif; ?>
</div>