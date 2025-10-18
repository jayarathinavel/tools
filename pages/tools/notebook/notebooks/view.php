<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Notebooks", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $userId = notebookUser();
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Notebooks</h1>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Created</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $notebooks = executeQuery("SELECT * FROM notebook WHERE user_id=$userId");
                    foreach ($notebooks as $notebook) {
                ?>
                <tr>
                    <td><?php echo $notebook['name']; ?></td>
                    <td><?php echo date('M d, Y', strtotime($notebook['created_at'])); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $notebook['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $notebook['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
    <a href="add.php" class="btn btn-primary mt-2">Add New Notebook</a>
</div>

<script>
    function confirmDelete() {
        return confirm("Deleting a notebook will also delete all its pages. Are you sure you want to delete this notebook?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
