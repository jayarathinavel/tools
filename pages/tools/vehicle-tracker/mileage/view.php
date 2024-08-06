<?php
    $pageTitle = "Mileage Records";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    $vehicleDetails = fetchVehicleDetails($vehicle);
    $mileageRecords = fetchMileageRecords($vehicle);
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Mileage Records</h1>
    <?php
        if(isset($vehicleDetails)) {
            echo "
                <div>
                    <h5>Vehicle :  " . $vehicleDetails['name'] . "</h5 >
                </div>
            ";
        }
    ?>
    <a href="add.php" class="btn btn-primary mb-3">Add New Record</a>
    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Fuel State</th>
                <th>Odometer Reading</th>
                <th>Comments</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($mileageRecords) && count($mileageRecords) > 0) { ?>
                <?php foreach ($mileageRecords as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['date']); ?></td>
                        <td><?php echo htmlspecialchars($record['fuel_state']); ?></td>
                        <td><?php echo htmlspecialchars($record['odometer_reading']); ?></td>
                        <td><?php echo htmlspecialchars($record['comments']); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo htmlspecialchars($record['id']); ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="delete.php?operation=delete&id=<?php echo htmlspecialchars($record['id']); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure want to delete this Mileage Record?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php } else { ?>
                <tr>
                    <td colspan="6" class="text-center">No Data found</td>
                </tr>
            <?php } ?>
        </tbody>
    </table>

</div>

<?php
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
