<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';

    // Delete entry via GET ?operation=delete&id=...
    if (isset($_GET['operation']) && $_GET['operation'] === 'delete' && isset($_GET['id'])) {
        try {
            $id = intval($_GET['id']);
            executeQuery("DELETE FROM cashbook_entry WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Entry Deleted Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Delete Entry! ' . $e->getMessage());
        }
        header("Location: view.php");
        exit;
    }

    // Edit entry (POST with id)
    if (isset($_POST['id'])) {
        try {
            $id = intval($_POST['id']);
            $conn = Database::getInstance()->getConnection();
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $amount = floatval($_POST['amount']);
            $type = mysqli_real_escape_string($conn, $_POST['type']);
            $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : "NULL";
            $bank_account_id = !empty($_POST['bank_account_id']) ? intval($_POST['bank_account_id']) : "NULL";
            $date = mysqli_real_escape_string($conn, $_POST['date']);
            executeQuery("UPDATE cashbook_entry SET title='$title', amount=$amount, type='$type', category_id=$category_id, bank_account_id=$bank_account_id, date='$date' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Entry Updated');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to update: '.$e->getMessage());
        }
        header("Location: view.php");
        exit;
    }

    // Add entry (POST without id)
    if (isset($_POST['title'])) {
        try {
            $conn = Database::getInstance()->getConnection();
            $title = mysqli_real_escape_string($conn, $_POST['title']);
            $amount = floatval($_POST['amount']);
            $type = mysqli_real_escape_string($conn, $_POST['type']);
            $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
            $bank_account_id = !empty($_POST['bank_account_id']) ? intval($_POST['bank_account_id']) : null;
            $date = mysqli_real_escape_string($conn, $_POST['date']);
            $bookId = intval($_POST['cashbook_book_id']);
            if ($type === 'transfer') {
                // For transfer, create two entries: transfer_out and transfer_in
                $to_account_id = !empty($_POST['to_account_id']) ? intval($_POST['to_account_id']) : null;
                if ($bank_account_id === $to_account_id) {
                    throw new Exception("From and To bank accounts cannot be the same for a transfer.");
                }
                // Outgoing entry
                executeQuery("INSERT INTO cashbook_entry (title, amount, type, category_id, bank_account_id, date, cashbook_book_id) VALUES('$title',$amount,'transfer_out',$category_id,$bank_account_id,'$date',$bookId)");
                // Incoming entry
                executeQuery("INSERT INTO cashbook_entry (title, amount, type, category_id, bank_account_id, date, cashbook_book_id) VALUES('$title',$amount,'transfer_in',$category_id,$to_account_id,'$date',$bookId)");
                setSuccessOrFailureMessage('success', 'Transfer Entry Added');
                header("Location: view.php");
                exit;
            }
            executeQuery("INSERT INTO cashbook_entry (title, amount, type, category_id, bank_account_id, date, cashbook_book_id) VALUES('$title',$amount,'$type',$category_id,$bank_account_id,'$date',$bookId)");
            setSuccessOrFailureMessage('success', 'Entry Added');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to add: '.$e->getMessage());
        }
        header("Location: view.php");
        exit;
    }
?>