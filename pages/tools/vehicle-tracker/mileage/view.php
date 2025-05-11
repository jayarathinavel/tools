<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Vehicle Tracker - Mileage", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    $mileageRecords = fetchMileageRecords($vehicle);
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <?php
        $calculatedMileage = [];
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
                $calculatedMileage[$date] = $mileage;
                // echo $date .'--->'. $mileage.'<br>';
                $firstFuelEmptyAt = [];
                $fuelQuantity = 0;
                $date = null;
            }
        }
    ?>
    <h1>Vehicle Tracker - Mileage</h1>
    <?php
       displayVehicleDetails($vehicle);
    ?>
    <div class="mt-2 mb-2">
        <?php echo isset($vehicle) ? '' : '<div class="text-danger mb-2"> No vehicles are available, <a href="/pages/tools/vehicle-tracker/vehicles/add.php">create a vehicle </a> first!</div>' ?>
        <a href="add.php" class="btn btn-primary <?php echo isset($vehicle) ? '' : 'disabled' ?>">Add New Record</a>
    </div>
    <div class='p-3 border' style='overflow-x: auto;'>
        <table class="table" id="mileage-records-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Fuel State</th>
                    <th>Odometer Reading</th>
                    <th>Mileage</th>
                    <th>Comments</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mileageRecords as $record): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($record['date']); ?></td>
                        <td><?php echo htmlspecialchars($record['fuel_state']); ?></td>
                        <td><?php echo htmlspecialchars($record['odometer_reading']); ?></td>
                        <td><?php echo isset($calculatedMileage[$record['date']]) ? round($calculatedMileage[$record['date']], 2) : '-'?></td>
                        <td><?php echo htmlspecialchars($record['comments']); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo htmlspecialchars($record['id']); ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                            <a href="delete.php?operation=delete&id=<?php echo htmlspecialchars($record['id']); ?>" class="btn btn-danger btn-sm m-1" onclick="return confirm('Are you sure want to delete this Mileage Record?');"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#mileage-records-table').DataTable({
            paging: false,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [4] }
            ],
            "bInfo": false,
        });
    });

</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
