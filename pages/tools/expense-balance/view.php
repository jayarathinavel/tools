<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Expense Balanace", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/expense-balance/expense-balance-utils.php');
    $userId = expenseBalanceUser();
    $book = findBookExpenseBalance($userId)
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Expense Balance</h1>
    <?php
        $expenses = [];
        $expenseBooks = executeQuery("SELECT * FROM expense_balance_book WHERE user_id=$userId");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['expenseBookId'])) {
                $book = intval($_POST['expenseBookId']);
                $_SESSION['expenseBalanceSelectedBook'] = $book;
                if (isset($_SESSION['appUserId'])) {
                    setUserDefaultBook('expense_balance', $book);
                }
                setSuccessOrFailureMessage("success", "Book Changed");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        if(isset($book)) {
            $expenses = executeQuery("SELECT * FROM expense_balance WHERE expense_balance_book_id = $book ORDER BY date DESC, id DESC");
            $persons = fetchPersonsFromExpenseBalanceBook($book);
        }
    ?>
    <div class="text-center">
        <?php if(isset($book)) { ?>
            <div class="mt-2">
                <span>Book: </span>
                <form style="display:inline" action="" method="POST">
                    <select name="expenseBookId" id="expenseBookId">
                        <?php while($row = mysqli_fetch_assoc($expenseBooks)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $book) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="submit" value="Change" class="btn btn-sm btn-primary">
                </form>
                <a class="btn btn-sm btn-secondary" href="books/view.php">Manage Books</a>
            </div>
        <?php } ?>
        <?php
        if (isset($expenses) && $expenses->num_rows > 0) {
            $totals = [];
            
            // Initialize totals for all persons with 0
            foreach ($persons as $person) {
                $totals[$person] = 0;
            }

            // Calculate total amounts spent by each person
            while ($row = $expenses->fetch_assoc()) {
                $person = $persons[$row['person']];
                $amount = $row['amount'];
                $totals[$person] += $amount;
            }

            // Find the maximum amount spent
            $maxAmount = max($totals);

            // Find the person who needs to spend next (person with the lowest amount spent)
            $personToSpendNext = null;
            $minAmount = PHP_INT_MAX;
            $firstPersonAmount = reset($totals);
            $allEqual = true;

            foreach ($totals as $person => $totalAmount) {
                if ($totalAmount < $minAmount) {
                    $minAmount = $totalAmount;
                    $personToSpendNext = $person;
                }

                // Check if all amounts are equal
                if ($totalAmount !== $firstPersonAmount) {
                    $allEqual = false;
                }
            }

            if (!$allEqual) {
                echo "<h4 class='mt-2'><span class='badge text-bg-warning'> The next person to spend is: " . $personToSpendNext . "</span></h4>";
            } else {
                echo "<h4 class='mt-2'><span class='badge text-bg-success'>Everyone has spent equally, it's Balanced!</span></h4>";
            }

            echo "
                <h5>Total Spends</h5>
                <ol style='list-style-type: none; padding-left: 0;'>
            ";
            foreach ($totals as $person => $totalAmount) {
                $difference = $maxAmount - $totalAmount;
                echo "<li>" . $person . " : " . $totalAmount . " ₹ ";
                echo "(To Balance: " . $difference . " ₹)</li>";
            }
            echo "</ol>";
        } else {
            echo "<h4>No expenses recorded.</h4>";
        }
        ?>

    </div>
    <div class="mt-2 mb-2">
        <?php echo isset($book) ? '' : '<div class="text-danger mb-2"> No books are available, <a href="books/add.php">create a book </a> first!</div>' ?>
        <a href="add.php" class="btn btn-primary <?php echo isset($book) ? '' : 'disabled' ?>" >Add New Expense</a>
    </div>
    <h4>Expenses List</h4>
    <div class="p-2" style="overflow-x: auto; border: 1px solid #DBDADA; border-radius: 5px; ">
        <table id="expenses-table" class="table">
            <thead>
                <tr>
                    <th>Expense Name</th>
                    <th>Amount</th>
                    <th>Person</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($expenses as $expense) {
                ?>
                <tr>
                    <td><?php echo $expense['expense_name']; ?></td>
                    <td><?php echo $expense['amount']; ?></td>
                    <td><?php echo $persons[$expense['person']]; ?></td>
                    <td><?php echo $expense['date']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $expense['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $expense['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('#expenses-table').DataTable({
            paging: false,
            "order": [[3, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [4] }
            ],
            "bInfo": false,
        });
    });

    function confirmDelete() {
        return confirm("Are you sure you want to delete this expense?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
