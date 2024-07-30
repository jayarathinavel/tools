<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Add Expense";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    setTodaysDateForForm();
?>
<div class="container">
    <h1>Add Expense</h1>
    <?php
        if (isset($_POST['expense_name'])) {
            require_once $rootPath . '/handlers/tools/expense-handler.php';
        }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="expense_name">Expense Name:</label>
            <input type="text" id="expense_name" name="expense_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="person">Person:</label>
            <select id="person" name="person" class="form-control" required>
                <option value="1">Person 1</option>
                <option value="2">Person 2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <input type="submit" value="Add Expense" class="btn btn-primary">
    </form>
</div>

<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
