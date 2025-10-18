<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Page", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    $userId = notebookUser();
    $notebook = findNotebook($userId)
?>
<div class="container">
    <h1>Add Page</h1>
    <?php
        if (isset($_POST['page_name'])) {
            includePhpFileFromRoot($rootPath, '/handlers/tools/notebook-page-handler.php');
        }
        if(isset($notebook)) {
            $notebooks = fetchNotebooks($userId);
        }
        else {
            echo '<div class="alert alert-danger mb-3" role="alert">No Notebook is available, create a notebook first!</div>';
        }
        
    ?>
    <form action="" method="post">
        <div class="fw-bold mb-2">
            <?php
                foreach ($notebooks as $id => $name):
                    if($id == intval($notebook)) {
                        echo 'Add New Page to ' . $name;
                    }
                endforeach;
            ?>
        </div>
        <div class="form-group">
            <label for="page_name">Page Name:</label>
            <input type="text" id="page_name" name="page_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="markdown_content">Markdown Content:</label>
            <textarea id="markdown_content" name="markdown_content" class="form-control" rows="10" required></textarea>
        </div>
        <input type="submit" value="Add Page" class="btn btn-primary mt-2" <?php echo isset($notebook) ? ' ' : 'disabled' ?>>
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
