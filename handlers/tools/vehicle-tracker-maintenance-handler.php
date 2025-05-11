<?php
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    require_once $rootPath . '/config/config.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $date = $_POST['date'];
        $vehicle_id = $_POST['vehicle'];
        $odometer_start = $_POST['odometer_start'];
        $odometer_due = $_POST['odometer_due'];
        $description = $_POST['description'];

        if (isset($_POST['id'])) { // Update record
            $id = $_POST['id'];
            executeQuery("UPDATE vt_maintenance  SET date='$date', vehicle_id='$vehicle_id', odometer_start='$odometer_start', odometer_due='$odometer_due', description='$description' WHERE id=$id");
            setSuccessOrFailureMessage("success", "Maintenance record updated successfully.");
        } else { // Add new record
            $vehicle_id = findVehicleForUser($userId);
            executeQuery("INSERT INTO vt_maintenance  (vehicle_id, date, odometer_start, odometer_due, description) VALUES ('$vehicle_id', '$date', '$odometer_start', '$odometer_due', '$description')");
            setSuccessOrFailureMessage("success", "Maintenance record added successfully.");
        }
        Database::getInstance()->closeConnection();
        header("Location: view.php");
        exit();
    }
