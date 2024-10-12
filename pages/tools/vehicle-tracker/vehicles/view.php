<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Vehicle Tracker - Vehicles", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
?>

<div class="container">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <h1>Vehicle Tracker - Vehicles</h1>
    <?php
        $result = executeQuery("SELECT * FROM vehicles");
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
                        <a href="edit.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-warning btn-sm m-1"><i class="bi bi-pencil-fill"></i></a>
                        <a href="delete.php?id=<?php echo htmlspecialchars($row['id']); ?>&operation=delete" class="btn btn-danger btn-sm m-1" onclick="return confirm('Are you sure ? Deleting vehicle will delete all vehicle records.');"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="add.php" class="btn btn-primary mb-3">Add New Vehicle</a>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
