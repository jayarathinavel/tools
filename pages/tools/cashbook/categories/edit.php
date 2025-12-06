<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Category", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    $id = intval($_GET['id']);
    $category = executeQuery("SELECT * FROM cashbook_category WHERE id=$id")->fetch_assoc();
    if (!$category) {
        echo '<div class="alert alert-danger">Category not found.</div>';
        exit;
    }
    if (isset($_POST['name'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-category-handler.php');
    }
?>
<div class="container">
    <form method="post" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="name">Category Name:</label>
            <input required type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($category['name']); ?>">
        </div>
        <button type="submit" class="btn btn-primary mt-2">Update Category</button>
    </form>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>