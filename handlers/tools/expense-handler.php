<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $userId = expenseBalanceUser();
    if (isset($_POST['id'])) {
        // Edit expense
        try {
            $id = $_POST['id'];
            $expense_name = $_POST['expense_name'];
            $description = $_POST['description'] ?? '';
            $amount = $_POST['amount'];
            $person = $_POST['person'];
            $date = $_POST['date'];
            executeQuery("UPDATE expense_balance SET expense_name='$expense_name', description='$description', amount='$amount', person='$person', date='$date' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Edited Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
        }
    } else {
        // Add new expense
        try {
            $expense_name = $_POST['expense_name'];
            $description = $_POST['description'] ?? '';
            $amount = $_POST['amount'];
            $person = $_POST['person'];
            $date = $_POST['date'];
            $book = findBookExpenseBalance($userId);
            executeQuery("INSERT INTO expense_balance (expense_name, description, amount, person, date, expense_balance_book_id)
                VALUES ('$expense_name', '$description', '$amount', '$person', '$date', '$book')");
            setSuccessOrFailureMessage('success', 'Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
        }
    }
    Database::getInstance()->closeConnection();
    header("Location: view.php");
    exit;
