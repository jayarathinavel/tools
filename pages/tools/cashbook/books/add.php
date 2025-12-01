<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Cashbook Book", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
?>
<div class="container">
    <h1>Add Cashbook Book</h1>
    <?php
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-book-handler.php');
    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input required type="text" id="name" name="name" class="form-control">
        </div>
        <button class="btn btn-primary mt-2" type="submit">Create</button>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>