<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $conn = initDb();
    initSuccessAndFailureMessage();
    if (isset($_POST['id'])) {
        // Edit expense
        try {
            $id = $_POST['id'];
            $expense_name = $_POST['expense_name'];
            $amount = $_POST['amount'];
            $person = $_POST['person'];
            $conn->query("UPDATE expenses SET expense_name='$expense_name', amount='$amount', person='$person' WHERE id=$id");
            successAndFailureMessage('success', 'Edited Successfully');
        } catch (Exception $e) {
            successAndFailureMessage('failure', 'Failed to Edit!' . $e->getMessage());
        }
    } else {
        // Add new expense
        try {
            $expense_name = $_POST['expense_name'];
            $amount = $_POST['amount'];
            $person = $_POST['person'];
            $conn->query("INSERT INTO expenses (expense_name, amount, person) VALUES ('$expense_name', '$amount', '$person')");
            successAndFailureMessage('success', 'Added Successfully');
        }
        catch (Exception $e) {
            successAndFailureMessage('failure', 'Failed to Add!' . $e->getMessage());
        }
    }
    header("Location: view.php");
    exit;
