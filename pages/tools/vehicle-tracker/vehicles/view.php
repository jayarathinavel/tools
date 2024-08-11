<?php
$pageTitle = "Vehicle Tracker - Vehicles";
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/pages/includes/main-pages/header.php';
includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
$conn = initDb();
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Vehicle Tracker - Vehicles</h1>
    <?php
        $result = $conn->query("SELECT * FROM vehicles");
    ?>
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-trash-fill"></i><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?id=<?php echo htmlspecialchars($row['id']); ?>&operation=delete" class="btn btn-danger btn-sm m-1" onclick="return confirm('Are you sure?');"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="add.php" class="btn btn-primary mb-3">Add New Vehicle</a>
</div>

<?php
require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
