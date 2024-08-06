<?php
    $pageTitle = "Add Mileage Record";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);

    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-mileage-handler.php');
    }

    $vehicles = fetchVehicles($userId);
?>

<div class="container">
    <h1>Add Mileage Record</h1>
    <form action="" method="post">
        <div class="fw-bold">
            <?php
                foreach ($vehicles as $id => $name):
                    if($id == intval($vehicle)) {
                        echo 'Add New Mileage Record to ' . $name;
                    }
                endforeach;
            ?>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="fuel_state">Fuel State:</label>
            <input type="text" id="fuel_state" name="fuel_state" class="form-control">
        </div>
        <div class="form-group">
            <label for="odometer_reading">Odometer Reading:</label>
            <input type="number" id="odometer_reading" name="odometer_reading" class="form-control" step="any" required>
        </div>
        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" class="form-control"></textarea>
        </div>
        <input type="submit" value="Add Record" class="btn btn-success mt-2">
    </form>
</div>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
    setTodaysDateForForm();
?>
