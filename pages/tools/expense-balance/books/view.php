<?php
    $pageTitle = "Expense Balance Books";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/expense-balance/expense-balance-utils.php');
    $userId = expenseBalanceUser();
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Expense Balance Books</h1>
    <div style="overflow-x: auto;">
        <table class="table">
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
                        <a href="edit.php?id=<?php echo $expenseBook['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $expenseBook['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <a href="add.php" class="btn btn-primary mt-2">Add New Expense Book</a>
</div>

<script>
    function confirmDelete() {
        return confirm("Deleteing a Expense balance book will also delete all its Entries. Are you sure you want to delete this book?");
    }
</script>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
