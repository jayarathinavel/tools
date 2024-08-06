<?php
includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
require_once $rootPath . '/config/config.php';
$conn = initDb();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['date'];
    $vehicle_id = $_POST['vehicle'];
    $start_distance = $_POST['start_distance'];
    $end_distance = $_POST['end_distance'];
    $comment = $_POST['comment'];
    
    if (isset($_POST['id'])) { // Update record
        $id = $_POST['id'];
        $conn->query("UPDATE odometer SET date='$date', vehicle_id='$vehicle_id', start_distance='$start_distance', end_distance='$end_distance', comment='$comment' WHERE id=$id");
        setSuccessOrFailureMessage("success", "Record updated successfully.");
    } else { // Add new record
        $vehicle_id = findVehicleForUser($userId);
        $conn->query("INSERT INTO odometer (vehicle_id, date, start_distance, end_distance, comment) VALUES ('$vehicle_id', '$date', '$start_distance', '$end_distance', '$comment')");
        setSuccessOrFailureMessage("success", "Record added successfully.");
    }
    header("Location: view.php");
    exit();
}
