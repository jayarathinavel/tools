<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    $pageTitle = "Add Expense";
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    setTodaysDateForForm();
    $userId = 1;
    $book = expenseBalanceFindBook($userId)
?>
<div class="container">
    <h1>Add Expense</h1>
    <?php
        if (isset($_POST['expense_name'])) {
            require_once $rootPath . '/handlers/tools/expense-handler.php';
        }
        if(isset($book)) {
            $persons = fetchPersonsFromExpenseBalanceBook($book);
        }
        else {
            echo '<div class="alert alert-danger mb-3" role="alert">No Book is available, create a book first!</div>';

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
                <?php
                    $i = 0;
                    foreach ($persons as $person): ?>
                    <option value="<?php echo $i ?>"><?php echo htmlspecialchars($person); ?></option>
                <?php
                    $i++;
                    endforeach;
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <input type="submit" value="Add Expense" class="btn btn-primary mt-2" <?php echo isset($book) ? ' ' : 'disabled' ?>>
    </form>
</div>

<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
