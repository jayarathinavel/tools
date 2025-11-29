<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
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
?>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <h1>Cashbook</h1>
    <div class="mb-3">
        <a class="btn btn-sm btn-primary" href="add.php">Add Entry</a>
        <a class="btn btn-sm btn-secondary" href="summary.php">Summary</a>
        <a class="btn btn-sm btn-info" href="books/view.php">Manage Books</a>
    </div>

    <form method="post" class="mb-3">
        <input type="hidden" name="filter" value="1">
        <div class="row g-2">
            <div class="col-auto">
                <select name="time_range" class="form-control">
                    <option value="">-- Time Range --</option>
                    <option value="this_month" <?php echo ($selectedTimeRange === 'this_month') ? 'selected' : ''; ?>>This Month</option>
                    <option value="last_month" <?php echo ($selectedTimeRange === 'last_month') ? 'selected' : ''; ?>>Last Month</option>
                    <option value="this_year" <?php echo ($selectedTimeRange === 'this_year') ? 'selected' : ''; ?>>This Year</option>
                    <option value="last_year" <?php echo ($selectedTimeRange === 'last_year') ? 'selected' : ''; ?>>Last Year</option>
                    <option value="custom" <?php echo ($selectedTimeRange === 'custom') ? 'selected' : ''; ?>>Custom</option>
                </select>
            </div>
            <div class="col-auto">
                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($selectedStartDate); ?>">
            </div>
            <div class="col-auto">
                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($selectedEndDate); ?>">
            </div>
            <div class="col-auto">
                <select name="category_id" class="form-control">
                    <option value="">-- Category --</option>
                    <?php foreach($categories as $id=>$n): ?>
                        <option value="<?php echo $id;?>" <?php echo ($selectedCategoryId !== '' && intval($selectedCategoryId) === intval($id)) ? 'selected' : ''; ?>><?php echo $n;?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <select name="bank_account_id" class="form-control">
                    <option value="">-- Bank Account --</option>
                    <?php foreach($bankAccounts as $a): ?>
                        <option value="<?php echo $a['id'];?>" <?php echo ($selectedBankAccountId !== '' && intval($selectedBankAccountId) === intval($a['id'])) ? 'selected' : ''; ?>><?php echo $a['name'];?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <?php if($book): ?>
        <table class="table">
            <thead><tr><th>Date</th><th>Title</th><th>Type</th><th>Amount</th><th>Category</th><th>Account</th><th>Action</th></tr></thead>
            <tbody>
                <?php while($e = mysqli_fetch_assoc($entries)): ?>
                    <tr>
                        <td><?php echo $e['date']; ?></td>
                        <td><?php echo htmlspecialchars($e['title']); ?></td>
                        <td><?php echo $e['type']; ?></td>
                        <td><?php echo $e['amount']; ?></td>
                        <td><?php echo $e['category_id'] ? (isset($categories[$e['category_id']]) ? $categories[$e['category_id']] : '') : ''; ?></td>
                        <td><?php echo $e['bank_account_id'] ? (isset($bankAccounts[$e['bank_account_id']]['name']) ? $bankAccounts[$e['bank_account_id']]['name'] : '') : ''; ?></td>
                        <td>
                            <a class="btn btn-sm btn-warning" href="edit.php?id=<?php echo $e['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
                            <a class="btn btn-sm btn-danger" href="delete.php?id=<?php echo $e['id']; ?>&operation=delete" onclick="return confirm('Delete this entry?');"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">No book selected / available.</div>
    <?php endif; ?>
</div>