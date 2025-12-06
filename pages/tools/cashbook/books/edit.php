<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Cashbook Book", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $id = intval($_GET['id']);
    $book = executeQuery("SELECT * FROM cashbook_book WHERE id=$id")->fetch_assoc();
    if (!$book) {
        echo '<div class="alert alert-danger">Book not found.</div>';
        exit;
    }
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-book-handler.php');
    }
?>
<div class="container">
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Book Name:</label>
            <input required type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($book['name']); ?>">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Update Book</button>
        <a href="view.php" class="btn btn-secondary mt-2">Cancel</a>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>