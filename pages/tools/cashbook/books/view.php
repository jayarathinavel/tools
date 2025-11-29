<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Cashbook Books", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
?>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <h1>Cashbook Books</h1>
    <div class="mb-3">
        <a class="btn btn-sm btn-primary" href="add.php">Add New Book</a>
    </div>
    <table class="table">
        <thead><tr><th>Name</th><th>Created</th><th>Action</th></tr></thead>
        <tbody>
            <?php
                $books = executeQuery("SELECT * FROM cashbook_book WHERE user_id=$userId");
                while($b = mysqli_fetch_assoc($books)):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($b['name']); ?></td>
                <td><?php echo $b['creation_timestamp']; ?></td>
                <td>
                    <a class="btn btn-sm btn-warning" title="Edit" href="edit.php?id=<?php echo $b['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
                    <a class="btn btn-sm btn-danger" title="Delete" href="delete.php?id=<?php echo $b['id']; ?>&operation=delete" onclick="return confirm('Deleting a book will also delete all its entries, accounts and categories. Are you sure?');"><i class="bi bi-trash-fill"></i></a>
                    <a class="btn btn-sm btn-info" title="View Entries" href="../view.php?book_id=<?php echo $b['id']; ?>"><i class="bi bi-journal-text"></i></a>
                    <a class="btn btn-sm btn-primary" title="View Accounts" href="../banks/view.php?book_id=<?php echo $b['id']; ?>"><i class="bi bi-people"></i></a>
                    <a class="btn btn-sm btn-secondary" title="View Categories" href="../categories/view.php?book_id=<?php echo $b['id']; ?>"><i class="bi bi-tags"></i></a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>