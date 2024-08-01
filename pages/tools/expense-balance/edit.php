<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Edit Expense";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    $id = $_GET['id'];
    $expense = $conn->query("SELECT * FROM expense_balance WHERE id=$id")->fetch_assoc();
    sessionStart();
    $userId = 1;
    $persons = fetchPersonsFromExpenseBalanceBook(expenseBalanceFindBook($userId));
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
            <select id="person" name="person" class="form-control" required>
                <?php
                    $i = 0;
                    foreach ($persons as $person): ?>
                    <option value="<?php echo $i ?>" <?php echo ($i == $expense['person']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($person); ?>
                    </option>
                <?php
                    $i++;
                    endforeach;
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" value="<?php echo $expense['date']; ?>">
        </div>
        <input type="submit" value="Update Expense" class="btn btn-primary mt-2">
    </form>
</div>


<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
