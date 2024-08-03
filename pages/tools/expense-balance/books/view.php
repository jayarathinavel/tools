<?php
    $pageTitle = "Expense Balance Books";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/expense-balance/expense-balance-utils.php');
    $userId = expenseBalanceUser();
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Expense Balance Books</h1>
    <div style="overflow-x: auto;">
        <table id="expense-books-table" class="table table-striped">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Persons</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $expenseBooks = $conn->query("SELECT * FROM expense_balance_book WHERE user_id=$userId");
                    foreach ($expenseBooks as $expenseBook) {
                ?>
                <tr>
                    <td><?php echo $expenseBook['name']; ?></td>
                    <td><?php echo $expenseBook['persons']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $expenseBook['id']; ?>" class="btn btn-primary">Edit</a>
                        <a href="delete.php?operation=delete&id=<?php echo $expenseBook['id']; ?>" class="btn btn-danger" onclick="return confirmDelete();">Delete</a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <a href="add.php" class="btn btn-success mt-2">Add New Expense Book</a>
    <a href="/pages/tools/expense-balance/view.php" class="btn btn-secondary mt-2">Back to Expenses</a>
</div>

<script>
    $(document).ready(function() {
        $('#expense-books-table').DataTable({
            paging: false,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [2] }
            ],
            "bInfo": false,
        });
    });

    function confirmDelete() {
        return confirm("Deleteing a Expense balance book will also delete all its Entries. Are you sure you want to delete this book?");
    }
</script>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
