<?php
    $pageTitle = "Expense Balance";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    initSuccessAndFailureMessage();
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
    <h1>Expenses</h1>
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
            $expenses = $conn->query("SELECT * FROM expense_balance");
            foreach ($expenses as $expense) {
                ?>
            <tr>
                <td><?php echo $expense['expense_name']; ?></td>
                <td><?php echo $expense['amount']; ?></td>
                <td><?php echo $expense['person']; ?></td>
                <td><?php echo $expense['date']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $expense['id']; ?>" class="btn btn-primary">Edit</a>
                    <a href="delete.php?operation=delete&id=<?php echo $expense['id']; ?>" class="btn btn-danger">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="add.php" class="btn btn-success">Add New Expense</a>
</div>

<script>
    $(document).ready(function() {
        $('#expenses-table').DataTable({
            paging: false,
            "order": [[3, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [2, 3] }
            ],
            "bInfo": false,
        });
    });
</script>

<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
