<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/config/config.php';
$conn = initDb();
$userId = expenseBalanceUser();

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
        setSuccessOrFailureMessage('success', 'Edited Successfully');
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Edit!' . ' ' . $e->getMessage());
    }
} else {
    // Add new expense book
    try {
        $name = $_POST['name'];
        $persons = $_POST['persons'];
        $conn->query("INSERT INTO expense_balance_book (name, persons, user_id) VALUES ('$name', '$persons', '$userId')");
        setSuccessOrFailureMessage('success', 'Added Successfully');
        clearSelectedBook();
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Add!' . ' ' . $e->getMessage());
    }
}

header("Location: view.php");
exit;
?>
