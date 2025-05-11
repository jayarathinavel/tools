<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');

    try {
        if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
            $id = $_GET['id'];
            $query = "DELETE FROM vt_vehicles WHERE id='$id'";
            if (executeQuery($query)) {
                $deleteOdometerQuery = "DELETE from vt_odometer WHERE vehicle_id = $id";
                $deleteMileageQuery = "DELETE from vt_mileage WHERE vehicle_id = $id";
                $deleteMaintenanceQuery = "DELETE from vt_maintenance WHERE vehicle_id = $id";
                $deleteNotesQuery = "DELETE from vt_notes WHERE vehicle_id = $id";
                executeQuery($deleteOdometerQuery);
                executeQuery($deleteMileageQuery);
                executeQuery($deleteMaintenanceQuery);
                executeQuery($deleteNotesQuery);
                clearSelectedVehicle();
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
