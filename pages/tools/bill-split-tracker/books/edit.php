<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Bill Split Book", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $id = $_GET['id'];
    $billSplitBook = executeQuery("SELECT * FROM bill_split_book WHERE id=$id")->fetch_assoc();
?>

<div class="container">
    <h1>Edit Bill Split Book</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/bill-split-book-handler.php');
    }
    ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Book Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($billSplitBook['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="persons">Persons:</label>
            <input type="text" id="persons" name="persons" class="form-control" value="<?php echo htmlspecialchars($billSplitBook['persons']); ?>" required>
            <small class="form-text text-muted">Note: You can add new persons if bills exist, but you cannot remove existing ones. You may also modify names.</small>
        </div>
        <input type="submit" value="Update Book" class="btn btn-primary mt-2">
        <a href="view.php" class="btn btn-secondary mt-2">Cancel</a>
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
