<?php
$pageTitle = "Edit Odometer Record";
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/pages/includes/main-pages/header.php';
appUserLoginRequired($_SERVER['REQUEST_URI']);
includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
$userId = vehicleTrackerUser();

// Check if the record ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo '<div class="alert alert-danger mb-3" role="alert">Invalid record ID!</div>';
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
    exit;
}

$recordId = intval($_GET['id']);
$record = findOdometerRecord($recordId);

if (!$record) {
    echo '<div class="alert alert-danger mb-3" role="alert">Record not found!</div>';
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-handler.php');
}

$vehicles = fetchVehicles($userId);

?>

<div class="container">
    <h1>Edit Odometer Record</h1>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $recordId; ?>">
        <div class="form-group">
            <label for="vehicle">Vehicle:</label>
            <select id="vehicle" name="vehicle" class="form-control" required>
                <?php foreach ($vehicles as $id => $name): ?>
                    <option value="<?php echo htmlspecialchars($id); ?>" <?php echo ($id == $record['vehicle_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($name); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($record['date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="start_distance">Start Distance:</label>
            <input type="number" id="start_distance" name="start_distance" class="form-control" step="any" value="<?php echo htmlspecialchars($record['start_distance']); ?>" required>
        </div>
        <div class="form-group">
            <label for="end_distance">End Distance:</label>
            <input type="number" id="end_distance" name="end_distance" class="form-control" step="any" value="<?php echo htmlspecialchars($record['end_distance']); ?>" required>
        </div>
        <div class="form-group">
            <label for="comment">Comment:</label>
            <textarea id="comment" name="comment" class="form-control"><?php echo htmlspecialchars($record['comment']); ?></textarea>
        </div>
        <input type="submit" value="Update Record" class="btn btn-success mt-2">
    </form>
</div>

<?php
appUserLoginRequiredClose();
require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
