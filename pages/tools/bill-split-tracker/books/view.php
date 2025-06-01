<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Bill Split Books", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $userId = billSplitUser();
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Bill Split Books</h1>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Persons</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $billSplitBooks = executeQuery("SELECT * FROM bill_split_book WHERE user_id=$userId ORDER BY created_at DESC");
                    foreach ($billSplitBooks as $billSplitBook) {
                ?>
                <tr>
                    <td><?php echo $billSplitBook['name']; ?></td>
                    <td>
                        <?php 
                            $persons = array_map('trim', explode(',', $billSplitBook['persons']));
                            foreach ($persons as $person) {
                                echo '<span class="badge bg-secondary me-1">' . htmlspecialchars($person) . '</span>';
                            }
                        ?>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($billSplitBook['created_at'])); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $billSplitBook['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $billSplitBook['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDeleteBook();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <a href="add.php" class="btn btn-primary mt-2">Add New Book</a>
</div>

<script>
    function confirmDeleteBook() {
        return confirm("Deleting a bill split book will also delete all its bills. Are you sure you want to delete this book?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
