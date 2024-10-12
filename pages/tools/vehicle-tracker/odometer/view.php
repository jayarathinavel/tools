<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Vehicle Tracker - Odometer", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
?>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.js"></script>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Vehicle Tracker - Odometer</h1>
    <?php
        $odometerRecords = [];
        if (isset($vehicle)) {
            $odometerRecords = executeQuery("SELECT * FROM odometer WHERE vehicle_id = $vehicle");
        }
    ?>
    <?php
       displayVehicleDetails($vehicle);
    ?>
    <div class="mt-2 mb-2">
        <?php echo isset($vehicle) ? '' : '<div class="text-danger mb-2"> No vehicles are available, <a href="/pages/tools/vehicle-tracker/vehicles/add.php">create a vehicle </a> first!</div>' ?>
        <a href="add.php" class="btn btn-primary <?php echo isset($vehicle) ? '' : 'disabled' ?>">Add New Odometer Record</a>
    </div>
 
    <div class='p-3 border' style='overflow-x: auto;'>
        <table id='odometer-table' class='table'>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Start Distance</th>
                    <th>End Distance</th>
                    <th>Distance</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($odometerRecords as $record) {
                ?>
                    <tr>
                        <td><?php echo $record['date']; ?></td>
                        <td><?php echo $record['start_distance']; ?></td>
                        <td><?php echo $record['end_distance']; ?></td>
                        <td><?php echo round(($record['end_distance'] - $record['start_distance']), 2); ?></td>
                        <td><?php echo $record['comment']; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $record['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                            <a href="delete.php?operation=delete&id=<?php echo $record['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                <?php
                    }
                ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#odometer-table').DataTable({
            paging: false,
            "order": [[0, "desc"]],
            "columnDefs": [
                { "orderable": false, "targets": [5] }
            ],
            "bInfo": false,
        });
    });

    function confirmDelete() {
        return confirm("Are you sure you want to delete this odometer record?");
    }
</script>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
