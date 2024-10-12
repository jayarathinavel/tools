<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Expense Book", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/expense-balance/expense-balance-utils.php');
?>

<div class="container">
    <h1>Add Expense Book</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/expense-balance-book-handler.php');
    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="persons">Persons: <span class="fw-light">(Comma Seperated Values)</span></label>
            <input type="text" placeholder="Ant, Bee, Cat" id="persons" name="persons" class="form-control" required>
        </div>
        <input type="submit" value="Add Expense Book" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
