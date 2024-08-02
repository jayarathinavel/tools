<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $conn = initDb();
    sessionStart();
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            $conn->query("DELETE FROM expense_balance WHERE id=$id");
            successAndFailureMessage('success', 'Deleted Successfully');
        }
    } catch (Exception $e) {
        successAndFailureMessage('failure', 'Failed to Delete!' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
