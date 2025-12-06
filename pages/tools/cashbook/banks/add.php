<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Bank Account", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-bank-handler.php');
    }
?>
<div class="container">
    <?php if(!$book) echo '<div class="alert alert-danger">No book selected. Create or select a book first.</div>'; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="name">Account Name:</label>
            <input required type="text" id="name" name="name" class="form-control">
        </div>
        <div class="form-group">
            <label for="initial_balance">Initial Balance:</label>
            <input required type="number" step="0.01" id="initial_balance" name="initial_balance" class="form-control" value="0.00">
        </div>
        <input type="hidden" name="cashbook_book_id" value="<?php echo $book; ?>">
        <button type="submit" class="btn btn-primary mt-2">Add Account</button>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>