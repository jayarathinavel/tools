<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Page", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $id = $_GET['id'];
    $page = executeQuery("SELECT * FROM notebook_page WHERE id=$id")->fetch_assoc();
?>

<div class="container">
    <h1>Edit Page</h1>
    <?php
        if (isset($_POST['page_name'])) {
            includePhpFileFromRoot($rootPath, '/handlers/tools/notebook-page-handler.php');
        }
    ?>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="form-group">
            <label for="page_name">Page Name:</label>
            <input type="text" id="page_name" name="page_name" class="form-control" value="<?php echo $page['page_name']; ?>">
        </div>
        <div class="form-group">
            <label for="markdown_content">Markdown Content:</label>
            <textarea id="markdown_content" name="markdown_content" class="form-control" rows="10"><?php echo htmlspecialchars($page['markdown_content']); ?></textarea>
        </div>
        <input type="submit" value="Update Page" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
