<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Cashbook Entry", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    if (isset($_POST['title'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-handler.php');
    }
    $accounts = $book ? fetchBankAccountsFromCashbook($book) : [];
    $categories = $book ? fetchCategoriesFromCashbook($book) : [];
?>
<div class="container">
    <h1>Add Entry</h1>
    <?php if(!$book) echo '<div class="alert alert-danger">No book. Create one first.</div>'; ?>
    <form method="post" action="">
        <div class="form-group">
            <label>Title</label>
            <input required name="title" class="form-control">
        </div>
        <div class="form-group">
            <label>Amount</label>
            <input required name="amount" type="number" step="0.01" class="form-control">
        </div>
        <div class="form-group">
            <label>Type</label>
            <select name="type" class="form-control">
                <option value="expense">Expense</option>
                <option value="income">Income</option>
            </select>
        </div>
        <div class="form-group">
            <label>Category <small><a href="categories/add.php">Add</a></small></label>
            <select name="category_id" class="form-control">
                <option value="">-- none --</option>
                <?php foreach($categories as $id => $name): ?>
                    <option value="<?php echo $id; ?>"><?php echo $name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Bank Account <small><a href="banks/add.php">Add</a></small></label>
            <select name="bank_account_id" class="form-control">
                <option value="">-- none --</option>
                <?php foreach($accounts as $a): ?>
                    <option value="<?php echo $a['id']; ?>"><?php echo $a['name']; ?> (<?php echo $a['initial_balance']; ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Date</label>
            <input type="date" name="date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
        </div>
        <input type="hidden" name="cashbook_book_id" value="<?php echo $book; ?>">
        <button class="btn btn-primary mt-2" type="submit">Save</button>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>