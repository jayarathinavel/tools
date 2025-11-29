<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $userId = cashbookUser();
    
    if (isset($_GET['operation']) && $_GET['operation'] === 'delete') {
        // Delete book and cascade
        try {
            $id = intval($_GET['id']);
            // Delete cascade: entries, bank accounts, categories
            executeQuery("DELETE FROM cashbook_entry WHERE cashbook_book_id=$id");
            executeQuery("DELETE FROM cashbook_bank_account WHERE cashbook_book_id=$id");
            executeQuery("DELETE FROM cashbook_category WHERE cashbook_book_id=$id");
            executeQuery("DELETE FROM cashbook_book WHERE id=$id AND user_id=$userId");
            clearSelectedBookCashbook();
            setSuccessOrFailureMessage('success', 'Book Deleted Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Delete! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    } elseif (isset($_POST['id'])) {
        // Edit book
        try {
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['name']);
            executeQuery("UPDATE cashbook_book SET name='$name' WHERE id=$id AND user_id=$userId");
            setSuccessOrFailureMessage('success', 'Book Updated Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Update! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    } else {
        // Add book
        try {
            $name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['name']);
            executeQuery("INSERT INTO cashbook_book (name, user_id) VALUES('$name', $userId)");
            setSuccessOrFailureMessage('success', 'Book Created Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Create! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    }
?>