<?php
    $pageTitle = "Vehicle Tracker";
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/pages/includes/main-pages/header.php';
    appUserLoginRequired($_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
?>

<div class="container mt-5">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <?php
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
    ?>
    <div class="row justify-content-center">
        <div class="col-md-4 mb-3">
            <div class="text-center border rounded p-3">
                <?php if (isset($vehicle)) { ?>
                    <div class="mt-2">
                        <form style="display:inline" action="" method="POST">
                            <h5><label for="vehicleId">Vehicle</label></h5>
                            <select class="form-select m-2" name="vehicleId" id="vehicleId">
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 border rounded">
                <div class="me-3">
                    <i class="bi bi-speedometer2 fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-1">Odometer</h5>
                    <a href="odometer/view.php" class="btn btn-link">Manage Odometer</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 border rounded">
                <div class="me-3">
                    <i class="bi bi-speedometer fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-1">Mileage</h5>
                    <a href="mileage/view.php" class="btn btn-link">Manage Mileage</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-center p-3 border rounded">
                <div class="me-3">
                    <i class="bi bi-car-front fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-1">Vehicles</h5>
                    <a href="vehicles/view.php" class="btn btn-link">Manage Vehicles</a>
                </div>
            </div>
        </div>
    </div>
</div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<?php
    appUserLoginRequiredClose();
    require_once $rootPath . '/pages/includes/main-pages/footer.php';
?>
