<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userId = vehicleTrackerUser();
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];

        try {
            if ($id) {
                // Update existing vehicle
                $query = "UPDATE vt_vehicles SET name='$name', description='$description' WHERE id='$id'";
                if (executeQuery($query)) {
                    setSuccessOrFailureMessage('success', 'Updated Successfully');
                } else {
                    throw new Exception('Error executing query');
                }
            } else {
                // Insert new vehicle
                $query = "INSERT INTO vt_vehicles (name, description, user_id) VALUES ('$name', '$description', '$userId')";
                if (executeQuery($query)) {
                    setSuccessOrFailureMessage('success', 'Added Successfully');
                    clearSelectedVehicle();
                } else {
                    throw new Exception('Error executing query');
                }
            }
        } catch (Exception $e) {
            setSuccessOrFailureMessage('failure', 'Failed to Save! ' . $e->getMessage());
        }
        Database::getInstance()->closeConnection();
        header("Location: view.php");
        exit;
    }
