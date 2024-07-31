<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $conn = initDb();
    sessionStart();
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            $conn->query("DELETE FROM expense_balance_book WHERE id=$id");
            successAndFailureMessage('success', 'Deleted Successfully');
            if(isset($_SESSION['expenseBalanceSelectedBook'])){
                unset($_SESSION['expenseBalanceSelectedBook']);
            }
        }
    } catch (Exception $e) {
        successAndFailureMessage('failure', 'Failed to Delete!' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
