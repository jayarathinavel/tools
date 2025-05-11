<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Vehicle Tracker - Maintenance", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    $maintenanceRecords = fetchMaintenanceRecords($vehicle);
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <?php
        
    ?>
    <h1>Vehicle Tracker - Maintenance </h1>
    <?php
       displayVehicleDetails($vehicle);
    ?>
    <div class="mt-2 mb-2">
        <?php echo isset($vehicle) ? '' : '<div class="text-danger mb-2"> No vehicles are available, <a href="/pages/tools/vehicle-tracker/vehicles/add.php">create a vehicle </a> first!</div>' ?>
        <a href="add.php" class="btn btn-primary <?php echo isset($vehicle) ? '' : 'disabled' ?>">Add New Record</a>
    </div>
    <div class='p-3 border' style='overflow-x: auto;'>
    <table class="table" id="maintenance-records-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Odometer Start</th>
                <th>Odometer Due</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($maintenanceRecords as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['date']); ?></td>
                    <td><?php echo htmlspecialchars($record['odometer_start']); ?></td>
                    <td><?php echo htmlspecialchars($record['odometer_due']); ?></td>
                    <td><?php echo htmlspecialchars($record['description']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo htmlspecialchars($record['id']); ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo htmlspecialchars($record['id']); ?>" class="btn btn-danger btn-sm m-1" onclick="return confirm('Are you sure you want to delete this Maintenance Record?');"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        $('#maintenance-records-table').DataTable({
            paging: false,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [3, 4] }
            ],
            "bInfo": false,
        });
    });
</script>


<?php
    initializePageFooter($rootPath, $moduleType);
?>
