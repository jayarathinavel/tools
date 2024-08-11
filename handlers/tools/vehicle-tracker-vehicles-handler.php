<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = initDb();
    $userId = vehicleTrackerUser();
    $id = isset($_POST['id']) ? $conn->real_escape_string($_POST['id']) : null;
    $name = $conn->real_escape_string($_POST['name']);
    $description = $conn->real_escape_string($_POST['description']);

    try {
        if ($id) {
            // Update existing vehicle
            $query = "UPDATE vehicles SET name='$name', description='$description' WHERE id='$id'";
            if ($conn->query($query)) {
                setSuccessOrFailureMessage('success', 'Updated Successfully');
            } else {
                throw new Exception('Error executing query: ' . $conn->error);
            }
        } else {
            // Insert new vehicle
            $query = "INSERT INTO vehicles (name, description, user_id) VALUES ('$name', '$description', '$userId')";
            if ($conn->query($query)) {
                setSuccessOrFailureMessage('success', 'Added Successfully');
            } else {
                throw new Exception('Error executing query: ' . $conn->error);
            }
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Save! ' . $e->getMessage());
    }

    header("Location: view.php");
    exit;
}
