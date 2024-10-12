<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Vehicle Tracker", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    $latestOdometerRecord = fetchLatestOdometerRecord($vehicle);
    $latestMileageRecords = fetchLatestMileageAndFuelQuantity($vehicle);
    $latestMileageRecord = $latestMileageRecords['latest_fuel_empty'];
    $latestFuelQuantityRecord = $latestMileageRecords['latest_fuel_quantity'];
    $maintenanceRecords = fetchMaintenanceRecords($vehicle);
?>

<div class="container mt-5">
    <?php
        getSuccessOrFailureMessage();
    ?>
    <div class="alert alert-primary <?php echo isset($vehicle) ? 'd-none' : ''?>" role="alert">
        Add a vechicle to get started!
    </div>
    <?php
        $vehicles = executeQuery("SELECT * FROM vehicles WHERE user_id=$userId");
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
    <!-- Vehicle Selection -->
    <div class="row justify-content-center <?php echo !isset($vehicle) ? 'd-none' : ''?>">
        <div class="col-md-6 col-lg-4 mb-3">
            <div class="text-center border rounded p-3">
                <div class="mt-2">
                    <form action="" method="POST">
                        <h5><label for="vehicleId">Vehicle</label></h5>
                        <div class="d-flex justify-content-center align-items-center">
                            <select class="form-select m-2" name="vehicleId" id="vehicleId" style="width:10rem;">
                                <?php while ($row = mysqli_fetch_assoc($vehicles)): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $vehicle) ? 'selected' : ''; ?>>
                                        <?php echo $row['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <input type="submit" value="Change" class="btn btn-sm btn-primary ms-2">
                        </div>
                    </form>

                    <div class="mt-2 d-flex justify-content-center">
                        <a class="btn btn-link pe-0" href="/pages/tools/vehicle-tracker/vehicles/view.php">Manage Vehicles</a>
                        <a class="btn btn-link" href="/pages/tools/vehicle-tracker/notes/view.php">Vehicle Notes</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Odometer -->
    <div class="row">
        <div class="col-md-4 mb-3 <?php echo !isset($vehicle) ? 'd-none' : ''?>">
            <div class="d-flex align-items-start p-3 border rounded">
                <div class="me-3">
                    <i class="bi bi-speedometer2 fs-2"></i>
                </div>
                <div>
                    <?php
                        if (isset($_POST['date'])) {
                            includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-handler.php');
                        }
                    ?>
                    <h5 class="mb-1">Odometer</h5>
                    <form action="" method="post">
                        <?php if(isset($latestOdometerRecord) && $latestOdometerRecord['end_distance'] != 0) { ?>
                            <div class="fw-bold">
                                Distance travelled on <?php echo formatDate($latestOdometerRecord['date']) ?> :
                                <?php echo round(($latestOdometerRecord['end_distance'] - $latestOdometerRecord['start_distance']), 2) ?> kms
                            </div>
                            <div class="fw-light mb-2">Add start distance for <?php echo formatDate(getNextDayDate($latestOdometerRecord['date'])) ?></div>
                            <!-- <div class="fw-light mb-2">Today is <?php //echo formatDate(date("Y-m-d")) ?></div> -->
                            <input type="date" value="<?php echo getNextDayDate($latestOdometerRecord['date']) ?>" id="date" name="date" class="form-control" hidden required>
                            <input type="number" value="0" id="end_distance" name="end_distance" hidden required>
                            <div class="form-group d-flex align-items-center">
                                <input type="number" value="<?php echo $latestOdometerRecord['end_distance'] ?>" id="start_distance" name="start_distance" class="form-control me-2" step="any" required>
                                <button type="submit" class="btn btn-primary d-flex align-items-center">
                                    <i class="bi bi-check"></i> <!-- Bootstrap check icon -->
                                </button>
                            </div>
                        <?php } elseif(isset($latestOdometerRecord) && $latestOdometerRecord['end_distance'] == 0){?>
                            <div class="fw-bold mb-2">Add end distance for <?php echo $latestOdometerRecord['date'] ?></div>
                            <div class="fw-light mb-2">Started on <?php echo $latestOdometerRecord['start_distance'] ?> kms</div>
                            <input type="hidden" name="id" value="<?php echo $latestOdometerRecord['id']; ?>">
                            <input type="hidden" name="vehicle" value="<?php echo $latestOdometerRecord['vehicle_id']; ?>">
                            <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($latestOdometerRecord['date']); ?>" required hidden>
                            <input type="number" id="start_distance" name="start_distance" class="form-control" step="any" value="<?php echo htmlspecialchars($latestOdometerRecord['start_distance']); ?>" required hidden>
                            <div class="form-group">
                                <input type="number" id="end_distance" name="end_distance" class="form-control" step="any" placeholder="End Distance" required>
                            </div>
                            <div class="form-row d-flex align-items-center mt-2">
                                <div class="form-group">
                                    <textarea id="comment" rows = "1" name="comment" placeholder = "Any Comments ?" class="form-control"><?php echo htmlspecialchars($latestOdometerRecord['comment']); ?></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary ms-2 d-flex align-items-center">
                                    <i class="bi bi-check"></i>
                                </button>
                            </div>
                        <?php } ?>
                    </form>
                    <a href="odometer/view.php" class="btn btn-link">Manage Odometer</a>
                </div>
            </div>
        </div>
        <!-- Mileage -->
        <div class="col-md-4 mb-3 <?php echo !isset($vehicle) ? 'd-none' : ''?>">
            <div class="d-flex align-items-start p-3 border rounded">
                <div class="me-3">
                    <i class="bi bi-speedometer fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-1">Mileage</h5>
                    <?php if(!empty($latestMileageRecord) || !empty($latestFuelQuantityRecord) ){ ?>
                        <p class='mb-0'>Lastest Readings:</p>
                    <?php } ?>
                    <?php if(!empty($latestMileageRecord)){ ?>
                        <p class='mb-0'>
                            <span class="badge bg-success"><?php echo date('d M', strtotime($latestMileageRecord['date'])) ?></span>
                            Mileage at
                            <?php echo $latestMileageRecord['odometer_reading']?>
                            kms = <?php echo round($latestMileageRecord['calculated_mileage'], 2)?> kms
                        </p>
                    <?php } ?>
                    <?php if(!empty($latestFuelQuantityRecord)){ ?>
                        <p class='mb-0'>
                            <span class="badge bg-success"><?php echo date('d M', strtotime($latestFuelQuantityRecord['date'])) ?></span>
                            Fuel at
                            <?php echo $latestFuelQuantityRecord['odometer_reading']?>
                            kms = <?php echo $latestFuelQuantityRecord['fuel_state']?> L
                        </p>
                    <?php } ?>
                    <a href="mileage/view.php" class="btn btn-link">Manage Mileage</a>
                </div>
            </div>
        </div>
        <!-- Vehicles -->
        <div class="col-md-4 mb-3">
            <div class="d-flex align-items-start p-3 border rounded">
                <div class="me-3">
                    <i class="bi bi-wrench fs-2"></i>
                </div>
                <div>
                    <h5 class="mb-1">Maintenance </h5>
                    <div>
                        <?php
                            if(!empty($latestOdometerReading)){
                                $latestOdometerReading = ($latestOdometerRecord['end_distance']) == 0 ? ($latestOdometerRecord['start_distance']) : ($latestOdometerRecord['end_distance']);
                                $upcomingMaintenances = [];

                                foreach ($maintenanceRecords as $record) {
                                    $kilometersToDue = $record['odometer_due'] - $latestOdometerReading;

                                    if ($kilometersToDue <= 1000 && $kilometersToDue >= 0) {
                                        $upcomingMaintenances[] = $record;
                                    }
                                }

                                if (!empty($upcomingMaintenances)) {
                                    echo "Maintenance due within the next 1000 km:<br>";
                                    foreach ($upcomingMaintenances as $maintenance) {
                                        echo "<ul class='mb-0'>";
                                        echo "<li><span class='fw-bold'>" . htmlspecialchars($maintenance['description']) . "</span>, at: " . htmlspecialchars($maintenance['odometer_due']) . " kms </li>";
                                        echo "</ul>";
                                    }
                                } else {
                                    echo "<p class='mb-0'>No maintenance due within the next 1000 km.</p>";
                                }
                            }
                        ?>
                    </div>
                    <a href="maintenance/view.php" class="btn btn-link">Manage Maintenance </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    initializePageFooter($rootPath, $moduleType);
?>
