<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/bill-split-tracker/bill-split-utils.php');
    $userId = billSplitUser();

    if (isset($_POST['id'])) {
        // Edit bill split book
        try {
            $id = $_POST['id'];
            $existingPersons = fetchPersonsFromBillSplitBook($id); // returns array
            $name = $_POST['name'];
            $persons = $_POST['persons'];

            $newPersonsArray = array_map('trim', explode(",", $persons));
            $existingPersonsArray = array_map('trim', $existingPersons);

            $billCount = executeQuery("SELECT COUNT(*) as count FROM bill_split WHERE bill_split_book_id=$id")->fetch_assoc()['count'];

            if ($billCount > 0) {
                $removedPersons = array_diff($existingPersonsArray, $newPersonsArray);
                if (count($removedPersons) > 0) {
                    throw new Exception('You cannot remove existing persons when bills exist! You may only add new persons or rename existing ones.');
                }
            }

            $name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $name);
            $persons = mysqli_real_escape_string(Database::getInstance()->getConnection(), $persons);

            executeQuery("UPDATE bill_split_book SET name='$name', persons='$persons' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Book Updated Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Update Book! ' . $e->getMessage());
        }
    } else {
        // Add new bill split book
        try {
            $name = $_POST['name'];
            $persons = $_POST['persons'];

            $personsArray = array_map('trim', explode(',', $persons));
            if (count($personsArray) < 2) {
                throw new Exception('Please add at least 2 persons.');
            }

            $name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $name);
            $persons = mysqli_real_escape_string(Database::getInstance()->getConnection(), $persons);

            executeQuery("INSERT INTO bill_split_book (name, persons, user_id, created_at) VALUES ('$name', '$persons', '$userId', NOW())");
            setSuccessOrFailureMessage('success', 'Book Created Successfully');
            clearSelectedBookBillSplit();
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Create Book! ' . $e->getMessage());
        }
    }

    Database::getInstance()->closeConnection();
    header("Location: view.php");
    exit;
?>
