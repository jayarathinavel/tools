<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Bank Accounts", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $userId = cashbookUser();
    $book = findBookCashbook($userId);
?>
<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <h1>Bank Accounts</h1>
    <?php if(!$book) echo '<div class="alert alert-danger">No book selected.</div>'; ?>
    <div class="mb-3">
        <a class="btn btn-sm btn-primary" href="add.php">Add Account</a>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                if($book) {
                    $accounts = executeQuery("SELECT * FROM cashbook_bank_account WHERE cashbook_book_id=$book ORDER BY creation_timestamp DESC");
                    while($a = mysqli_fetch_assoc($accounts)):
            ?>
            <tr>
                <td><?php echo htmlspecialchars($a['name']); ?></td>
                <td>
                    <a class="btn btn-sm btn-warning" href="edit.php?id=<?php echo $a['id']; ?>"><i class="bi bi-pencil-fill"></i></a>
                    <a class="btn btn-sm btn-danger" href="delete.php?id=<?php echo $a['id']; ?>&operation=delete" onclick="return confirm('Delete this account?');"><i class="bi bi-trash-fill"></i></a>
                </td>
            </tr>
            <?php endwhile; } ?>
        </tbody>
    </table>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>