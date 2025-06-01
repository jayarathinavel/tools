<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Bill Split Book", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
?>

<div class="container">
    <h1>Add Bill Split Book</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/bill-split-book-handler.php');
    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Book Name:</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="e.g., Trip to Goa" required>
        </div>
        <div class="form-group">
            <label for="persons">Persons: <span class="fw-light">(Comma Separated Values)</span></label>
            <input type="text" placeholder="John, Jane, Bob" id="persons" name="persons" class="form-control" required>
            <small class="form-text text-muted">Enter at least 2 person names separated by commas</small>
        </div>
        <input type="submit" value="Create Book" class="btn btn-primary mt-2">
        <a href="view.php" class="btn btn-secondary mt-2">Cancel</a>
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
