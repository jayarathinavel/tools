<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/config/config.php';
$conn = initDb();
sessionStart();

if (isset($_POST['id'])) {
    // Edit expense book
    try {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $persons = $_POST['persons'];
        $conn->query("UPDATE expense_balance_book SET name='$name', persons='$persons' WHERE id=$id");
        successAndFailureMessage('success', 'Edited Successfully');
    } catch (Exception $e) {
        successAndFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
    }
} else {
    // Add new expense book
    try {
        $user = 1;
        $name = $_POST['name'];
        $persons = $_POST['persons'];
        $conn->query("INSERT INTO expense_balance_book (name, persons, user_id) VALUES ('$name', '$persons', '$user')");
        successAndFailureMessage('success', 'Added Successfully');
    } catch (Exception $e) {
        successAndFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
    }
}

header("Location: view.php");
exit;
?>
