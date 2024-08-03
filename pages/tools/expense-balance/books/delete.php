<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $conn = initDb();
    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            $conn->query("DELETE FROM expense_balance_book WHERE id=$id");
            $conn->query("DELETE FROM expense_balance WHERE expense_balance_book_id=$id");
            setSuccessOrFailureMessage('success', 'Deleted Successfully');
            if(isset($_SESSION['expenseBalanceSelectedBook'])){
                unset($_SESSION['expenseBalanceSelectedBook']);
            }
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete!' . $e->getMessage());
    }
    header("Location: view.php");
    exit;
