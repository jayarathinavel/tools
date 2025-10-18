<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/events-anniversary/events-anniversary-utils.php');
    $userId = eventsAnniversaryUser();

    try {
        if (isset($_POST['id'])) {
            $id = intval($_POST['id']);
            $name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['name']);
            $note = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['note']);
            $original = $_POST['original_date'];
            executeQuery("UPDATE events_anniversary SET name='$name', note='$note', original_date='$original' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Edited Successfully');
        } else {
            $name = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['name']);
            $note = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['note']);
            $original = $_POST['original_date'];
            executeQuery("INSERT INTO events_anniversary (user_id, name, note, original_date, added_date)
                          VALUES ('$userId', '$name', '$note', '$original', NOW())");
            setSuccessOrFailureMessage('success', 'Added Successfully');
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Operation failed! ' . $e->getMessage());
    }

    Database::getInstance()->closeConnection();
    header("Location: view.php");
    exit;
