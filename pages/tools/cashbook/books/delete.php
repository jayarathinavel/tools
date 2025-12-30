<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Delete Cashbook Book", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    if (isset($_GET['operation']) && $_GET['operation'] === 'delete' && isset($_GET['id'])) {
        canEditBook($_GET['id']);
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-book-handler.php');
    }
?>
<?php
    initializePageFooter($rootPath, $moduleType);
?>