<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/config/config.php';
$conn = initDb();
sessionStart();
$userId = 1;

if (isset($_POST['id'])) {
    // Edit expense book
    try {
        $id = $_POST['id'];
        $existingPersons = fetchPersonsFromExpenseBalanceBook($id);
        $name = $_POST['name'];
        $persons = $_POST['persons'];
        if(count($existingPersons) != count(array_map('trim', explode("," , $persons)))) {
            throw new Exception('You cannot add or remove persons! You can just modify the name.');
        }
        $conn->query("UPDATE expense_balance_book SET name='$name', persons='$persons' WHERE id=$id");
        successAndFailureMessage('success', 'Edited Successfully');
    } catch (Exception $e) {
        successAndFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
    }
} else {
    // Add new expense book
    try {
        $name = $_POST['name'];
        $persons = $_POST['persons'];
        $conn->query("INSERT INTO expense_balance_book (name, persons, user_id) VALUES ('$name', '$persons', '$userId')");
        successAndFailureMessage('success', 'Added Successfully');
    } catch (Exception $e) {
        successAndFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
    }
}

header("Location: view.php");
exit;
?>
