<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            executeQuery("DELETE FROM bill_split_book WHERE id=$id");
            executeQuery("DELETE FROM bill_split WHERE bill_split_book_id=$id");
            setSuccessOrFailureMessage('success', 'Book Deleted Successfully');
            clearSelectedBookBillSplit();
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete!' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
?>
