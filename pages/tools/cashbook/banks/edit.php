<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Bank Account", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $id = intval($_GET['id']);
    $account = executeQuery("SELECT * FROM cashbook_bank_account WHERE id=$id")->fetch_assoc();
    if (!$account) {
        echo '<div class="alert alert-danger">Account not found.</div>';
        exit;
    }
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-bank-handler.php');
    }
?>
<div class="container">
    <h1>Edit Bank Account</h1>
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Account Name:</label>
            <input required type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($account['name']); ?>">
        </div>
        <div class="form-group">
            <label for="initial_balance">Initial Balance:</label>
            <input required type="number" step="0.01" id="initial_balance" name="initial_balance" class="form-control" value="<?php echo $account['initial_balance']; ?>">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Update Account</button>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>