<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    
    if (isset($_GET['operation']) && $_GET['operation'] === 'delete') {
        // Delete category
        try {
            $id = intval($_GET['id']);
            $cashbookBookId = executeQuery("SELECT cashbook_book_id FROM cashbook_category WHERE id=$id")->fetch_object()->cashbook_book_id;
            if ($cashbookBookId == 0) {
                throw new Exception("Cannot delete default category.");
            }
            executeQuery("DELETE FROM cashbook_category WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Category Deleted Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Delete! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    } elseif (isset($_POST['id'])) {
        // Edit category
        try {
            $id = intval($_POST['id']);
            $conn = Database::getInstance()->getConnection();
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            executeQuery("UPDATE cashbook_category SET name='$name' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Category Updated Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Update! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    } else {
        // Add category
        try {
            $conn = Database::getInstance()->getConnection();
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            $bookId = intval($_POST['cashbook_book_id']);
            executeQuery("INSERT INTO cashbook_category (name, cashbook_book_id) VALUES('$name', $bookId)");
            setSuccessOrFailureMessage('success', 'Category Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    }
?>