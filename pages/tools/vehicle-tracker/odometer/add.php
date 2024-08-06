<?php
$pageTitle = "Add Odometer Record";
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/pages/includes/main-pages/header.php';
appUserLoginRequired($_SERVER['REQUEST_URI']);
includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
$userId = vehicleTrackerUser();
$vehicle = findVehicleForUser($userId);
?>

<div class="container">
    <h1>Add Odometer Record</h1>
    <?php
    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-handler.php');
    }
    if(isset($vehicle)) {
        $vehicles = fetchVehicles($userId);
    }
    else {
        echo '<div class="alert alert-danger mb-3" role="alert">No Vehicle is available, create a vehicle first!</div>';

    }
    ?>
    <form action="" method="post">
        <div class="form-group">
            <label for="vehicle">Vehicle:</label>
            <select id="vehicle" name="vehicle" class="form-control" required>
                <?php foreach ($vehicles as $id => $name): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>">
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="start_distance">Start Distance:</label>
            <input type="number" id="start_distance" name="start_distance" class="form-control" step="any" required>
        </div>
        <div class="form-group">
            <label for="end_distance">End Distance:</label>
            <input type="number" id="end_distance" name="end_distance" class="form-control" step="any" required>
        </div>
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" class="form-control"></textarea>
        </div>
        <input type="submit" value="Add Record" class="btn btn-success mt-2">
    </form>
</div>

<?php
appUserLoginRequiredClose();
require_once $rootPath . '/pages/includes/main-pages/footer.php';
setTodaysDateForForm();
?>
