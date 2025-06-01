<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $userId = billSplitUser();
    
    if (isset($_POST['id'])) {
        // Edit bill
        try {
            $id = $_POST['id'];
            $bill_name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['bill_name']);
            $total_amount = floatval($_POST['total_amount']);
            $paid_by = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['paid_by']);
            $split_type = $_POST['split_type'];
            $date = $_POST['date'];
            $splits_json = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['splits_json']);
            
            executeQuery("UPDATE bill_split SET bill_name='$bill_name', total_amount='$total_amount', paid_by='$paid_by', split_type='$split_type', date='$date', splits='$splits_json' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Bill Updated Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Update Bill! ' . $e->getMessage());
        }
    } else {
        // Add new bill
        try {
            $bill_name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['bill_name']);
            $total_amount = floatval($_POST['total_amount']);
            $paid_by = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['paid_by']);
            $split_type = $_POST['split_type'];
            $date = $_POST['date'];
            $splits_json = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['splits_json']);
            $book = findBookBillSplit($userId);
            
            if (!$book) {
                throw new Exception('No book selected. Please select a book first.');
            }
            
            executeQuery("INSERT INTO bill_split (bill_name, total_amount, paid_by, split_type, date, splits, bill_split_book_id, created_at) 
                VALUES ('$bill_name', '$total_amount', '$paid_by', '$split_type', '$date', '$splits_json', '$book', NOW())");
            setSuccessOrFailureMessage('success', 'Bill Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add Bill! ' . $e->getMessage());
        }
    }
    
    Database::getInstance()->closeConnection();
    header("Location: view.php");
    exit;
?>
