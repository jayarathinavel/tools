<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $conn = initDb();
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    if (isset($_POST['id'])) {
        // Edit note
        try {
            $id = $_POST['id'];
            $date = $_POST['date'];
            $note = $_POST['note'];
            $conn->query("UPDATE vt_notes SET date='$date', note='$note' WHERE id=$id");
            setSuccessOrFailureMessage('success', 'Note Updated Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Update Note! ' . $e->getMessage());
        }
    } else {
        // Add new note
        try {
            $date = $_POST['date'];
            $note = $_POST['note'];
            $conn->query("INSERT INTO vt_notes (vehicle_id, date, note) VALUES ('$vehicle', '$date', '$note')");
            setSuccessOrFailureMessage('success', 'Note Added Successfully');
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Add Note! ' . $e->getMessage());
        }
    }

    header("Location: view.php");
    exit;
?>
