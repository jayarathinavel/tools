<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Category", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-category-handler.php');
    }
?>
<div class="container">
    <h1>Add Category</h1>
    <?php if(!$book) echo '<div class="alert alert-danger">No book selected. Create or select a book first.</div>'; ?>
    <form method="post" action="">
        <div class="form-group">
            <label for="name">Category Name:</label>
            <input required type="text" id="name" name="name" class="form-control">
        </div>
        <input type="hidden" name="cashbook_book_id" value="<?php echo $book; ?>">
        <button type="submit" class="btn btn-primary mt-2">Add Category</button>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>