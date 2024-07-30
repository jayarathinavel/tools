<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Edit Expense";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    $id = $_GET['id'];
    $expense = $conn->query("SELECT * FROM expenses WHERE id=$id")->fetch_assoc();
?>

<div class="container">
    <h1>Edit Expense</h1>
    <?php
        if (isset($_POST['expense_name'])) {
            require_once $rootPath . '/handlers/tools/expense-handler.php';
        }
    ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="expense_name">Expense Name:</label>
            <input type="text" id="expense_name" name="expense_name" class="form-control" value="<?php echo $expense['expense_name']; ?>">
        </div>
        <div class="form-group">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" class="form-control" value="<?php echo $expense['amount']; ?>">
        </div>
        <div class="form-group">
            <label for="person">Person:</label>
            <select id="person" name="person" class="form-control">
                <option value="1" <?php if ($expense['person'] == 1) echo 'selected'; ?>>Person 1</option>
                <option value="2" <?php if ($expense['person'] == 2) echo 'selected'; ?>>Person 2</option>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" value="<?php echo $expense['date']; ?>">
        </div>
        <input type="submit" value="Update Expense" class="btn btn-primary">
    </form>
</div>


<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
