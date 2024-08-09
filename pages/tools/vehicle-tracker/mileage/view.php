<?php
    $pageTitle = "Mileage Records";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    $mileageRecords = fetchMileageRecords($vehicle);
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <?php
        $mileage = [];
        $nextFuelEmptyAt = [];
        $firstFuelEmptyAt = [];
        $fuelQuantity = null;
        foreach ($mileageRecords as $mileageRecord){
            $fuelState = $mileageRecord["fuel_state"];
            if($fuelState == "Fuel Empty"){
                if(!isset($nextFuelEmptyAt['odometer_reading'])){
                    $nextFuelEmptyAt = ['date' => $mileageRecord["date"], 'odometer_reading' => $mileageRecord["odometer_reading"]];
                    continue;
                }
                if(isset($nextFuelEmptyAt['odometer_reading'])){
                    $date = $nextFuelEmptyAt['date'];
                    $firstFuelEmptyAt = ['date' => $mileageRecord["date"], 'odometer_reading' => $mileageRecord["odometer_reading"]];
                }
            }
            $fuelQuantityInt = doubleval($mileageRecord["fuel_state"]);
            if($fuelQuantityInt > 0 && isset($nextFuelEmptyAt['odometer_reading'])) {
                $fuelQuantity += $fuelQuantityInt;
            }
            if(isset($nextFuelEmptyAt['odometer_reading']) && isset($firstFuelEmptyAt['odometer_reading']) && $fuelQuantity > 0) {
                $mileage = ($nextFuelEmptyAt['odometer_reading'] - $firstFuelEmptyAt['odometer_reading'])/$fuelQuantity;
                $nextFuelEmptyAt = $firstFuelEmptyAt;
                echo $date .'--->'. $mileage.'<br>';
                $firstFuelEmptyAt = [];
                $fuelQuantity = 0;
                $date = null;
            }
        }
    ?>
    <h1>Mileage Records</h1>
    <?php
       displayVehicleDetails($vehicle);
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
