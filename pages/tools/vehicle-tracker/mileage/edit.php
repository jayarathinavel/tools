<?php
    $pageTitle = "Edit Mileage Record";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $appUserId = vehicleTrackerUser();

    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-mileage-handler.php');
    }

    $id = $_GET['id'];
    $mileageRecord = findMileageRecord($id);
    $vehicles = fetchVehicles($appUserId);
?>

<div class="container">
    <h1>Edit Mileage Record</h1>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
        <div class="form-group">
            <label for="vehicle">Vehicle:</label>
            <select id="vehicle" name="vehicle" class="form-control" required>
                <?php foreach ($vehicles as $vehicleId => $vehicleName): ?>
                    <option value="<?php echo htmlspecialchars($vehicleId); ?>" <?php echo $mileageRecord['vehicle_id'] == $vehicleId ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($vehicleName); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="datetime" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($mileageRecord['date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="fuel_state">Fuel State:</label>
            <input type="text" id="fuel_state" name="fuel_state" class="form-control" value="<?php echo htmlspecialchars($mileageRecord['fuel_state']); ?>">
        </div>
        <div class="form-group">
            <label for="odometer_reading">Odometer Reading:</label>
            <input type="number" id="odometer_reading" name="odometer_reading" class="form-control" step="any" value="<?php echo htmlspecialchars($mileageRecord['odometer_reading']); ?>" required>
        </div>
        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" class="form-control"><?php echo htmlspecialchars($mileageRecord['comments']); ?></textarea>
        </div>
        <input type="submit" value="Update Record" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
