<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Edit Expense Book";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/expense-balance/expense-balance-utils.php');
    $id = $_GET['id'];
    $expenseBook = $conn->query("SELECT * FROM expense_balance_book WHERE id=$id")->fetch_assoc();
?>

<div class="container">
    <h1>Edit Expense Book</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/expense-balance-book-handler.php');
    }
    ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo $expenseBook['name']; ?>">
        </div>
        <div class="form-group">
            <label for="persons">Persons:</label>
            <input type="text" id="persons" name="persons" class="form-control" value="<?php echo $expenseBook['persons']; ?>">
        </div>
        <input type="submit" value="Update Expense Book" class="btn btn-primary mt-2">
    </form>
</div>

<?php
require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
