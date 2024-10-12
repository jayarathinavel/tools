<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/expense-balance/expense-balance-utils.php');
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            executeQuery("DELETE FROM expense_balance_book WHERE id=$id");
            executeQuery("DELETE FROM expense_balance WHERE expense_balance_book_id=$id");
            setSuccessOrFailureMessage('success', 'Deleted Successfully');
            clearSelectedBook();
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete!' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
