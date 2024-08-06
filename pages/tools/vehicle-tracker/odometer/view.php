<?php
$pageTitle = "Vehicle Tracker - Odometer";
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/pages/includes/main-pages/header.php';
appUserLoginRequired($_SERVER['REQUEST_URI']);
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
    $vehicles = $conn->query("SELECT * FROM vehicles WHERE user_id=$userId");
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['vehicleId'])) {
            $vehicle = $_POST['vehicleId'];
            $_SESSION['vehicleTrackerSelectedVehicle'] = $vehicle;
            setSuccessOrFailureMessage("success", "Vehicle Changed");
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }
    if (isset($vehicle)) {
        $odometerRecords = $conn->query("SELECT * FROM odometer WHERE vehicle_id = $vehicle");
    }
    ?>
    <div class="text-center">
        <?php if (isset($vehicle)) { ?>
            <div class="mt-2">
                <span>Vehicle: </span>
                <form style="display:inline" action="" method="POST">
                    <select name="vehicleId" id="vehicleId">
                        <?php while ($row = mysqli_fetch_assoc($vehicles)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $vehicle) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <input type="submit" value="Change" class="btn btn-sm btn-primary">
                </form>
                <a class="btn btn-sm btn-secondary" href="/pages/tools/vehicle-tracker/vehicles/view.php">Manage Vehicles</a>
            </div>
        <?php } ?>
    </div>
    <div class="mt-2 mb-2">
        <?php echo isset($vehicle) ? '' : '<div class="text-danger mb-2"> No vehicles are available, <a href="/pages/tools/vehicle-tracker/vehicles/add.php">create a vehicle </a> first!</div>' ?>
        <a href="add.php" class="btn btn-success <?php echo isset($vehicle) ? '' : 'disabled' ?>">Add New Odometer Record</a>
    </div>
 
    <h4>Odometer Records</h4>
    <div class='p-2' style='overflow-x: auto; border: 1px solid #DBDADA; border-radius: 5px;'>
        <table id='odometer-table' class='table table-striped'>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Start Distance</th>
                    <th>End Distance</th>
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
                        <td><?php echo $record['comment']; ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $record['id']; ?>" class="btn btn-primary">Edit</a>
                            <a href="delete.php?operation=delete&id=<?php echo $record['id']; ?>" class="btn btn-danger" onclick="return confirmDelete();">Delete</a>
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
                { "orderable": false, "targets": [4] }
            ],
            "bInfo": false,
        });
    });

    function confirmDelete() {
        return confirm("Are you sure you want to delete this odometer record?");
    }
</script>

<?php
appUserLoginRequiredClose();
require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
