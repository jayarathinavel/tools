<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Notebook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $id = $_GET['id'];
    $notebook = executeQuery("SELECT * FROM notebook WHERE id=$id")->fetch_assoc();
?>

<div class="container">
    <h1>Edit Notebook</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/notebook-handler.php');
    }
    ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Notebook Name:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo $notebook['name']; ?>">
        </div>
        <input type="submit" value="Update Notebook" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
