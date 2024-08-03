<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
$pageTitle = "Add Expense Book";
require_once $rootPath . '/pages/includes/main-pages/header.php';

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
            <label for="persons">Persons:</label>
            <input type="text" id="persons" name="persons" class="form-control" required>
        </div>
        <input type="submit" value="Add Expense Book" class="btn btn-primary mt-2">
    </form>
</div>

<?php
require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
