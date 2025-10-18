<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    require_once $_SERVER['DOCUMENT_ROOT'] . '/lib/Parsedown.php';
    $id = $_GET['id'];
    $page = executeQuery("SELECT * FROM notebook_page WHERE id=$id")->fetch_assoc();
    $parsedown = new Parsedown();
    
    initializePage($page['page_name'], "main", $_SERVER['REQUEST_URI']);
?>

<div class="container">
    <div class="mb-3">
        <a href="view.php" class="btn btn-secondary">Back to Notebook</a>
        <a href="edit.php?id=<?php echo $id; ?>" class="btn btn-warning"><i class="bi bi-pencil-fill"></i> Edit</a>
    </div>
    
    <h1><?php echo htmlspecialchars($page['page_name']); ?></h1>
    <small class="text-muted">Created: <?php echo date('M d, Y H:i', strtotime($page['created_at'])); ?></small>
    <small class="text-muted"> | Updated: <?php echo date('M d, Y H:i', strtotime($page['updated_at'])); ?></small>
    
    <hr>
    
    <div class="markdown-content" style="line-height: 1.6;">
        <?php echo $parsedown->text($page['markdown_content']); ?>
    </div>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
