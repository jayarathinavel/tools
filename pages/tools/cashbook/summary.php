<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook Summary", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    $accounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $balances = [];
    if($book){
        foreach($accounts as $a){
            $id = $a['id'];
            $initial = floatval($a['initial_balance']);
            $income = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='income'")->fetch_assoc()['sum'];
            $expense = executeQuery("SELECT IFNULL(SUM(amount),0) sum FROM cashbook_entry WHERE bank_account_id=$id AND type='expense'")->fetch_assoc()['sum'];
            $balances[$id] = $initial + floatval($income) - floatval($expense);
        }
    }
?>
<div class="container">
    <h1>Summary</h1>
    <?php if(!$book) echo '<div class="alert alert-danger">No book selected.</div>'; ?>
    <table class="table">
        <thead><tr><th>Account</th><th>Balance</th></tr></thead>
        <tbody>
            <?php foreach($accounts as $a): ?>
                <tr>
                    <td><?php echo $a['name']; ?></td>
                    <td><?php echo number_format($balances[$a['id']],2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>