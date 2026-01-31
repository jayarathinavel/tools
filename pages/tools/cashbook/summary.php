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
    $lends = [];
    $lendRepayments = [];
    $transfersIn = [];
    $transfersOut = [];
    $investments = [];
    if($book){
        $bookName = executeQuery("SELECT name FROM cashbook_book WHERE id={$book}")->fetch_assoc()['name'] ?? 'No Book Selected';
        foreach($accounts as $a){
            $id = $a['id'];
            $initial = floatval($a['initial_balance']);
            $income = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='income'")->fetch_assoc()['sum'];
            $expense = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='expense'")->fetch_assoc()['sum'];
            
            // Get adjustment amounts (non-income/expense transactions)
            $lend = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='lend'")->fetch_assoc()['sum'];
            $lendRepayment = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='lend_repayment'")->fetch_assoc()['sum'];
            $transferIn = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='transfer_in'")->fetch_assoc()['sum'];
            $transferOut = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='transfer_out'")->fetch_assoc()['sum'];
            $investment = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='investment'")->fetch_assoc()['sum'];
            
            // Balance: initial + income - expense - lend + lend_repayment
            $balances[$id] = $initial + floatval($income) - floatval($expense) - floatval($lend) + floatval($lendRepayment) + floatval($transferIn) - floatval($transferOut) - floatval($investment);
            $expenses[$id] = floatval($expense);
            $incomes[$id] = floatval($income);
            $lends[$id] = floatval($lend);
            $lendRepayments[$id] = floatval($lendRepayment);
            $transfersIn[$id] = floatval($transferIn);
            $transfersOut[$id] = floatval($transferOut);
            $investments[$id] = floatval($investment);
            $initials[$id] = $initial;
        }
    }
?>
<div class="container mt-4">
    <h2><?php echo $bookName; ?></h2>
    <?php if(!$book): ?>
        <div class="alert alert-danger">No book selected.</div>
    <?php else: ?>
        <div class="row justify-content-start mb-4 g-3">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Expenses</small>
                            <h5 class="fw-bold text-danger mb-0">
                                ₹ <?php
                                    $totalExpense = array_sum($expenses);
                                    echo number_format($totalExpense, 2);
                                ?>
                            </h5>
                        </div>
                        <div class="text-danger fs-3">
                            <i class="bi bi-arrow-down-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Investments</small>
                            <h5 class="fw-bold text-success mb-0">
                                ₹ <?php
                                    $totalInvestment = array_sum($investments);
                                    echo number_format($totalInvestment, 2);
                                ?>
                            </h5>
                        </div>
                        <div class="text-success fs-3">
                            <i class="bi bi-piggy-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Lends</small>
                            <h5 class="fw-bold text-danger mb-0">
                                ₹ <?php
                                    $totalLend = array_sum($lends);
                                    echo number_format($totalLend, 2);
                                ?>
                            </h5>
                        </div>
                        <div class="text-danger fs-3">
                            <i class="bi bi-send"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Lend Repayments</small>
                            <h5 class="fw-bold text-success mb-0">
                                ₹ <?php
                                    $totalLendRepayment = array_sum($lendRepayments);
                                    echo number_format($totalLendRepayment, 2);
                                ?>
                            </h5>
                        </div>
                        <div class="text-success fs-3">
                            <i class="bi bi-receipt"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <small class="text-muted text-uppercase">Income</small>
                            <h5 class="fw-bold text-success mb-0">
                                ₹ <?php
                                    $totalIncome = array_sum($incomes);
                                    echo number_format($totalIncome, 2);
                                ?>
                            </h5>
                        </div>
                        <div class="text-success fs-3">
                            <i class="bi bi-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row g-4">
            <?php foreach($accounts as $a):
                $id = $a['id'];
                $balance = $balances[$id];
                $balanceClass = $balance >= 0 ? 'text-success' : 'text-danger';
                $collapseId = "collapse{$id}";
                $headingId = "heading{$id}";
            ?>
                <?php if($initials[$id] > 0): ?>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="accordion shadow-sm" id="accordion-<?php echo $id; ?>">

                            <div class="accordion-item">
                                <h2 class="accordion-header" id="<?php echo $headingId; ?>">
                                    <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#<?php echo $collapseId; ?>"
                                            aria-expanded="false"
                                            aria-controls="<?php echo $collapseId; ?>">

                                        <div class="d-flex w-100 align-items-center">
                                            <span class="fw-bold">
                                                <?php echo htmlspecialchars($a['name']); ?>
                                            </span>

                                            <span class="ms-auto fw-bold me-2 <?php echo $balanceClass; ?>">
                                                ₹ <?php echo number_format($balance, 2); ?>
                                            </span>
                                        </div>
                                    </button>

                                </h2>

                                <div id="<?php echo $collapseId; ?>"
                                    class="accordion-collapse collapse">
                                    <div class="accordion-body">

                                        <div class="mb-2">
                                            <small class="text-muted">Initial Balance</small>
                                            <div>₹ <?php echo number_format($initials[$id], 2); ?></div>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">Income</small>
                                            <div class="text-success">+ ₹ <?php echo number_format($incomes[$id], 2); ?></div>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">Expense</small>
                                            <div class="text-danger">- ₹ <?php echo number_format($expenses[$id], 2); ?></div>
                                        </div>

                                        <?php if ($lends[$id] != 0): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Lends</small>
                                            <div class="text-danger">- ₹ <?php echo number_format($lends[$id], 2); ?></div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($lendRepayments[$id] != 0): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Lend Repayments</small>
                                            <div class="text-success">+ ₹ <?php echo number_format($lendRepayments[$id], 2); ?></div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($transfersIn[$id] != 0): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Transfers In</small>
                                            <div class="text-success">+ ₹ <?php echo number_format($transfersIn[$id], 2); ?></div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($transfersOut[$id] != 0): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Transfers Out</small>
                                            <div class="text-danger">- ₹ <?php echo number_format($transfersOut[$id], 2); ?></div>
                                        </div>
                                        <?php endif; ?>

                                        <?php if ($investments[$id] != 0): ?>
                                        <div class="mb-2">
                                            <small class="text-muted">Investments</small>
                                            <div class="text-danger">- ₹ <?php echo number_format($investments[$id], 2); ?></div>
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
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>