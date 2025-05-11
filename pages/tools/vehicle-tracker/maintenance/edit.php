<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Maintenance  Record", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $appUserId = vehicleTrackerUser();

    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-maintenance-handler.php');
    }

    $id = $_GET['id'];
    $maintenanceRecord = findMaintenanceRecord($id);
    $vehicles = fetchVehicles($appUserId);
?>

<div class="container">
    <h1>Edit Maintenance Record</h1>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        
        <div class="form-group">
            <label for="vehicle">Vehicle:</label>
            <select id="vehicle" name="vehicle" class="form-control" required>
                <?php foreach ($vehicles as $vehicleId => $vehicleName): ?>
                    <option value="<?php echo htmlspecialchars($vehicleId); ?>" <?php echo $maintenanceRecord['vehicle_id'] == $vehicleId ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($vehicleName); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($maintenanceRecord['date']); ?>" required>
        </div>

        <div class="form-group">
            <label for="odometer_start">Odometer Start:</label>
            <input type="number" id="odometer_start" name="odometer_start" class="form-control" step="any" value="<?php echo htmlspecialchars($maintenanceRecord['odometer_start']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="odometer_due">Odometer Due:</label>
            <input type="number" id="odometer_due" name="odometer_due" class="form-control" step="any" value="<?php echo htmlspecialchars($maintenanceRecord['odometer_due']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($maintenanceRecord['description']); ?></textarea>
        </div>
        
        <input type="submit" value="Update Record" class="btn btn-primary mt-2">
    </form>
</div>


<?php
    initializePageFooter($rootPath, $moduleType);
?>
