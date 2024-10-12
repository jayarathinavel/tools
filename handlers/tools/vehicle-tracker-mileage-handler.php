<?php
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    require_once $rootPath . '/config/config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $date = $_POST['date'];
        $vehicle_id = $_POST['vehicle'];
        $fuel_state = $_POST['fuel_state'];
        $comments = $_POST['comments'];
        $odometer_reading = $_POST['odometer_reading']; // New field

        if (isset($_POST['id'])) { // Update record
            $id = $_POST['id'];
            executeQuery("UPDATE vt_mileage SET date='$date', vehicle_id='$vehicle_id', fuel_state='$fuel_state', comments='$comments', odometer_reading='$odometer_reading' WHERE id=$id");
            setSuccessOrFailureMessage("success", "Record updated successfully.");
        } else { // Add new record
            $vehicle_id = findVehicleForUser($userId);
            executeQuery("INSERT INTO vt_mileage (vehicle_id, date, fuel_state, comments, odometer_reading) VALUES ('$vehicle_id', '$date', '$fuel_state', '$comments', '$odometer_reading')");
            setSuccessOrFailureMessage("success", "Record added successfully.");
        }
        Database::getInstance()->closeConnection();
        header("Location: view.php");
        exit();
    }
