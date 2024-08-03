<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $conn = initDb();
    $userId = expenseBalanceUser();
    if (isset($_POST['id'])) {
        // Edit expense
        try {
            $id = $_POST['id'];
            $expense_name = $_POST['expense_name'];
            $amount = $_POST['amount'];
            $person = $_POST['person'];
            $date = $_POST['date'];
            $conn->query("UPDATE expense_balance SET expense_name='$expense_name', amount='$amount', person='$person', date='$date' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Edited Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
        }
    } else {
        // Add new expense
        try {
            $expense_name = $_POST['expense_name'];
            $amount = $_POST['amount'];
            $person = $_POST['person'];
            $date = $_POST['date'];
            $book = findBookExpenseBalance($userId);
            $conn->query("INSERT INTO expense_balance (expense_name, amount, person, date, expense_balance_book_id)
                VALUES ('$expense_name', '$amount', '$person', '$date', '$book')");
            setSuccessOrFailureMessage('success', 'Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
        }
    }
    header("Location: view.php");
    exit;
