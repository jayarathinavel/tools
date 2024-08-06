<?php
$pageTitle = "Edit Vehicle";
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/pages/includes/main-pages/header.php';
appUserLoginRequired($_SERVER['REQUEST_URI']);
includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
$conn = initDb();

$vehicle = null;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $id = $conn->real_escape_string($id);
    $result = $conn->query("SELECT * FROM vehicles WHERE id='$id'");
    if ($result->num_rows == 1) {
        $vehicle = $result->fetch_assoc();
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $vehicle) {
    includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-vehicles-handler.php');
}
?>

<div class="container">
    <h1>Edit Vehicle</h1>
    <?php if ($vehicle): ?>
        <form action="" method="post">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($vehicle['id']); ?>">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($vehicle['name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($vehicle['description']); ?></textarea>
            </div>
            <input type="submit" value="Update Vehicle" class="btn btn-warning mt-2">
        </form>
    <?php else: ?>
        <div class="alert alert-danger">Vehicle not found!</div>
    <?php endif; ?>
</div>

<?php
appUserLoginRequiredClose();
require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
