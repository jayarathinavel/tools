<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Vehicle", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-vehicles-handler.php');
    }
?>

<div class="container">
    <h1>Add Vehicle</h1>
    <form action="" method="post">
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea id="description" name="description" class="form-control"></textarea>
        </div>
        <input type="submit" value="Add Vehicle" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
?>
