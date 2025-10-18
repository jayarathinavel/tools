<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';

    try {
        if (isset($_POST['event_id'])) {
            $eventId = intval($_POST['event_id']);
            $date = $_POST['celebration_date'];
            $note = mysqli_real_escape_string(Database::getInstance()->getConnection(), $_POST['celebration_note']);
            executeQuery("INSERT INTO events_anniversary_celebration (event_id, date, note, created_at)
                          VALUES ('$eventId', '$date', '$note', NOW())");
            setSuccessOrFailureMessage('success', 'Celebration Added');
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to add celebration! ' . $e->getMessage());
    }

    Database::getInstance()->closeConnection();
    header("Location: event.php?id=" . intval($_POST['event_id']));
    exit;
