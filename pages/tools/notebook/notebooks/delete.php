<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/notebook/notebook-utils.php');
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            executeQuery("DELETE FROM notebook_page WHERE notebook_id=$id");
            executeQuery("DELETE FROM notebook WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Deleted Successfully');
            clearSelectedNotebook();
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete!' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
?>
