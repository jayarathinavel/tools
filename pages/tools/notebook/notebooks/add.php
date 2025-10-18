<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Notebook", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
?>

<div class="container">
    <h1>Add Notebook</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/notebook-handler.php');
    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Notebook Name:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <input type="submit" value="Add Notebook" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
