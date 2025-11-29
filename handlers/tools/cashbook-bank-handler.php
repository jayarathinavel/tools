<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    
    if (isset($_GET['operation']) && $_GET['operation'] === 'delete') {
        // Delete bank account
        try {
            $id = intval($_GET['id']);
            executeQuery("DELETE FROM cashbook_bank_account WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Account Deleted Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Delete! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    } elseif (isset($_POST['id'])) {
        // Edit bank account
        try {
            $id = intval($_POST['id']);
            $conn = Database::getInstance()->getConnection();
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $initialBalance = floatval($_POST['initial_balance']);
            executeQuery("UPDATE cashbook_bank_account SET name='$name', initial_balance=$initialBalance WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Account Updated Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Update! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    } else {
        // Add bank account
        try {
            $conn = Database::getInstance()->getConnection();
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $initialBalance = floatval($_POST['initial_balance']);
            $bookId = intval($_POST['cashbook_book_id']);
            executeQuery("INSERT INTO cashbook_bank_account (name, initial_balance, cashbook_book_id) VALUES('$name', $initialBalance, $bookId)");
            setSuccessOrFailureMessage('success', 'Account Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    }
?>