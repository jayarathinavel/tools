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
            
            // Get adjustment amounts (non-income/expense transactions)
            $repayment = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='repayment'")->fetch_assoc()['sum'];
            $lend = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='lend'")->fetch_assoc()['sum'];
            $lendRepayment = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='lend_repayment'")->fetch_assoc()['sum'];
            $transferIn = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='transfer_in'")->fetch_assoc()['sum'];
            $transferOut = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='transfer_out'")->fetch_assoc()['sum'];
            $investment = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='investment'")->fetch_assoc()['sum'];
            
            // Balance: initial + income - expense - repayment - lend + lend_repayment
            $balances[$id] = $initial + floatval($income) - floatval($expense) - floatval($repayment) - floatval($lend) + floatval($lendRepayment) + floatval($transferIn) - floatval($transferOut) - floatval($investment);
            $expenses[$id] = floatval($expense);
            $incomes[$id] = floatval($income);
            $lends[$id] = floatval($lend);
            $lendRepayments[$id] = floatval($lendRepayment);
            $repayments[$id] = floatval($repayment);
            $transfersIn[$id] = floatval($transferIn);
            $transfersOut[$id] = floatval($transferOut);
            $investments[$id] = floatval($investment);
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
                            <?php if ($lends[$id] != 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Lends</small>
                                    <div class="fs-6 text-danger">- ₹ <?php echo number_format($lends[$id], 2); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($lendRepayments[$id] != 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Lend Repayments</small>
                                    <div class="fs-6 text-success">+ ₹ <?php echo number_format($lendRepayments[$id], 2); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($repayments[$id] != 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Repayments</small>
                                    <div class="fs-6 text-success">+ ₹ <?php echo number_format($repayments[$id], 2); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($transfersIn[$id] != 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Transfers In</small>
                                    <div class="fs-6 text-success">+ ₹ <?php echo number_format($transfersIn[$id], 2); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($transfersOut[$id] != 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Transfers Out</small>
                                    <div class="fs-6 text-danger">- ₹ <?php echo number_format($transfersOut[$id], 2); ?></div>
                                </div>
                            <?php endif; ?>
                            <?php if ($investments[$id] != 0): ?>
                                <div class="mb-3">
                                    <small class="text-muted">Investments</small>
                                    <div class="fs-6 text-success">+ ₹ <?php echo number_format($investments[$id], 2); ?></div>
                                </div>
                            <?php endif; ?>
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