<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            executeQuery("DELETE FROM vt_notes WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Note Deleted Successfully');
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete Note! ' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
?>
