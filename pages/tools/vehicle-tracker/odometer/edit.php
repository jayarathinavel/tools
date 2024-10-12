<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Edit Odometer Record", "main", $_SERVER['REQUEST_URI']);
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
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-handler.php');
    }

    $vehicles = fetchVehicles($userId);

?>

<div class="container">
    <h1>Edit Odometer Record</h1>
    <form action="" method="post">
        <input type="hidden" name="id" value="<?php echo $recordId; ?>">
        <div class="fw-bold">
            <?php
                foreach ($vehicles as $id => $name):
                    if($id == $record['vehicle_id']) {
                        echo 'Editing a Odometer Record of ' . $name;
                    }
                endforeach;
            ?>
        </div>
        <input type="hidden" name="vehicle" value="<?php echo $record['vehicle_id']; ?>">
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
        <input type="submit" value="Update Record" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
