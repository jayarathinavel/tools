<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');

    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            $query = "DELETE FROM odometer WHERE id='$id'";
            if (executeQuery($query)) {
                setSuccessOrFailureMessage('success', 'Deleted Successfully');
            } else {
                throw new Exception('Error executing query');
            }
        }
    } catch (Exception $e) {
        setSuccessOrFailureMessage('failure', 'Failed to Delete! ' . $e->getMessage());
    }

    header("Location: view.php");
    exit;
