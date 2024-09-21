<?php
    $pageTitle = "Add Maintainence Record";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);

    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-maintainence-handler.php');
    }

    $vehicles = fetchVehicles($userId);
?>

<div class="container">
    <h1>Add Maintenance Record</h1>
    <form action="" method="post" id="add-maintenance-record">
        <div class="fw-bold">
            <?php
                foreach ($vehicles as $id => $name):
                    if($id == intval($vehicle)) {
                        echo 'Add New Maintenance Record to ' . htmlspecialchars($name);
                    }
                endforeach;
            ?>
        </div>
        
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        
        <div class="form-group">
            <label for="odometer_start">Odometer Start:</label>
            <input type="number" id="odometer_start" name="odometer_start" class="form-control" step="any" required>
        </div>
        
        <div class="form-group">
            <label for="odometer_due">Odometer Due:</label>
            <input type="number" id="odometer_due" name="odometer_due" class="form-control" step="any" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>

        <input type="submit" value="Add Record" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
    setTodaysDateForForm();
?>
