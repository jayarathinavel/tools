<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Delete Cashbook Entry", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/cashbook/cashbook-utils.php');
    if (isset($_GET['operation']) && $_GET['operation'] === 'delete' && isset($_GET['id'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/cashbook-handler.php');
    }
?>
<?php
    initializePageFooter($rootPath, $moduleType);
?>