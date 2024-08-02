<?php
    $pageTitle = "Expense Balance";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    sessionStart();
    $userId = 1;
    $book = expenseBalanceFindBook($userId)
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        if (isset($_SESSION['status']) && isset($_SESSION['message'])) {
            $status = $_SESSION['status'];
            $message = $_SESSION['message'];
            if($status == 'success') {
                echo '<div class="alert alert-success mb-3" role="alert">' . $message . '</div>';
            } elseif($status == 'failure') {
                echo '<div class="alert alert-danger mb-3" role="alert">' . $message . '</div>';
            }
            unset($_SESSION['status']);
            unset($_SESSION['message']);
        }
    ?>
    <h1>Expense Balance</h1>
    <?php
        $expenses = [];
        $expenseBooks = $conn->query("SELECT * FROM expense_balance_book WHERE user_id=$userId");
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['expenseBookId'])) {
                $book = $_POST['expenseBookId'];
                $_SESSION['expenseBalanceSelectedBook'] = $book;
                successAndFailureMessage("success", "Book Changed");
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        }
        if(isset($book)) {
            $expenses = $conn->query("SELECT * FROM expense_balance WHERE expense_balance_book_id = $book");
            $persons = fetchPersonsFromExpenseBalanceBook($book);
        }
    ?>
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
        </div>
    <?php } ?>
    <?php
        if(isset($expenses) && $expenses->num_rows > 0) {
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
            }
            if ($totalAmount !== $firstPersonAmount) {
                $allEqual = false;
            }
            if (!$allEqual) {
                echo "<h4 class='mt-2'><span class='badge text-bg-warning'> The next person to spend is: " . $personToSpendNext . "</span></h4>";
            } else {
                echo "<h4 class='mt-2'><span class='badge text-bg-success'>Everyone has spent equally, its Balanced!</span></p>";
            }

            echo "
                <h5>Total Spends</h5>
                <ol>
            ";
            foreach ($totals as $person => $totalAmount) {
                echo "<li>". $person . " : " . $totalAmount . " ₹ </li>";
            }
            echo "</ol>";
        }
    ?>
    <div class="mt-2 mb-2">
        <?php echo isset($book) ? '' : '<div class="text-danger mb-2"> No books are available, create a book first!</div>' ?>
        <a href="add.php" class="btn btn-success <?php echo isset($book) ? '' : 'disabled' ?>" >Add New Expense</a>
        <a class="btn btn-secondary" href="books/view.php">Manage Books</a>
    </div>
    <h4>Expenses List</h4>
    <div class="p-2" style="overflow-x: auto; border: 1px solid #DBDADA; border-radius: 5px; ">
        <table id="expenses-table" class="table table-striped">
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
                        <a href="edit.php?id=<?php echo $expense['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete.php?operation=delete&id=<?php echo $expense['id']; ?>" class="btn btn-danger" onclick="return confirmDelete();">Delete</a>
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
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
