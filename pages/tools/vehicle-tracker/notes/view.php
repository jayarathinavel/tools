<?php
    $pageTitle = "Vehicle Tracker - Notes";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
?>

<div class="container">
    <?php getSuccessOrFailureMessage(); ?>
    <h1>Vehicle Tracker - Notes</h1>
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Note</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $notes = $conn->query("SELECT * FROM vt_notes WHERE vehicle_id=$vehicle ORDER BY date DESC");
                    foreach ($notes as $note) {
                ?>
                <tr>
                    <td><?php echo $note['date']; ?></td>
                    <td><?php echo $note['note']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $note['id']; ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?operation=delete&id=<?php echo $note['id']; ?>" class="btn btn-danger btn-sm m-1" onclick="return confirmDelete();"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <a href="add.php" class="btn btn-primary mt-2">Add New Note</a>
</div>

<script>
    function confirmDelete() {
        return confirm("Are you sure you want to delete this note?");
    }
</script>

<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
