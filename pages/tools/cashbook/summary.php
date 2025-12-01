<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook Summary", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    $accounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $balances = [];
    $expenses = [];
    $incomes = [];
    $initials = [];
    if($book){
        foreach($accounts as $a){
            $id = $a['id'];
            $initial = floatval($a['initial_balance']);
            $income = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='income'")->fetch_assoc()['sum'];
            $expense = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='expense'")->fetch_assoc()['sum'];
            $balances[$id] = $initial + floatval($income) - floatval($expense);
            $expenses[$id] = floatval($expense);
            $incomes[$id] = floatval($income);
            $initials[$id] = $initial;
        }
    }
?>
<div class="container mt-4">
    <h1 class="mb-4">Summary</h1>
    <?php if(!$book): ?>
        <div class="alert alert-danger">No book selected.</div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach($accounts as $a): 
                $id = $a['id'];
                $balance = $balances[$id];
                $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
            ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($a['name']); ?></h5>
                            <hr>
                            <div class="mb-3">
                                <small class="text-muted">Initial Balance</small>
                                <div class="fs-6">₹ <?php echo number_format($initials[$id], 2); ?></div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Total Income</small>
                                <div class="fs-6 text-success">+ ₹ <?php echo number_format($incomes[$id], 2); ?></div>
                            </div>
                            <div class="mb-3">
                                <small class="text-muted">Total Expense</small>
                                <div class="fs-6 text-danger">- ₹ <?php echo number_format($expenses[$id], 2); ?></div>
                            </div>
                            <hr>
                            <div>
                                <small class="text-muted">Current Balance</small>
                                <div class="fs-5 fw-bold <?php echo $balanceClass; ?>">₹ <?php echo number_format($balance, 2); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>