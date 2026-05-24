<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Odometer - Quick Entry", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);
    $latestOdometerRecord = fetchLatestOdometerRecord($vehicle);
    $previousOdometerRecord = fetchPreviousOdometerRecord($vehicle);
    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-handler.php');
    }
    $vehicleDetails = fetchVehicleDetails($vehicle);
?>

<div class="container div">
    <div class="row justify-content-center <?php echo !isset($vehicle) ? 'd-none' : ''?>">
        <div class="col-md-6 col-lg-4 mb-3">
            <?php getSuccessOrFailureMessage(); ?>
            <h5 class="mb-1"> <i class="bi-lightning"></i> Odometer - Quick Entry</h5>
            <p><b>Vehicle: </b> <?php echo $vehicleDetails['name'] ?> </p>
            <form action="" method="post">
                <?php if (isset($latestOdometerRecord) && $latestOdometerRecord['end_distance'] != 0) { ?>
                    <div class="fw-bold">
                        Distance travelled on
                        <?php echo formatDate($latestOdometerRecord['date']) ?> :
                        <?php echo round(($latestOdometerRecord['end_distance'] - $latestOdometerRecord['start_distance']), 2) ?>
                        kms
                    </div>

                    <div class="fw-light mb-2">
                        Add start distance for
                        <?php echo formatDate(getNextDayDate($latestOdometerRecord['date']))?>
                    </div>

                    <input type="date" value="<?php echo getNextDayDate($latestOdometerRecord['date']) ?>" id="date" name="date" class="form-control" hidden required>

                    <input type="number" value="0" id="end_distance" name="end_distance" hidden required>

                    <div class="form-group d-flex align-items-center">
                        <input type="number" value="<?php echo $latestOdometerRecord['end_distance'] ?>" id="start_distance" name="start_distance" class="form-control me-2" step="any"
                            inputmode="decimal" autofocus required>
                        <button type="submit" class="btn btn-primary d-flex align-items-center">
                            Save
                        </button>
                    </div>

                <?php } elseif ( isset($latestOdometerRecord) && $latestOdometerRecord['end_distance'] == 0 ) { ?>

                    <?php
                        $suggestedEndDistance = $latestOdometerRecord['start_distance'] + 25;
                        if (isset($previousOdometerRecord) && $previousOdometerRecord['end_distance'] > 0 ) {
                            $previousTripDistance = $previousOdometerRecord['end_distance'] - $previousOdometerRecord['start_distance'];
                            $suggestedEndDistance = $latestOdometerRecord['start_distance'] + $previousTripDistance;
                        }
                    ?>

                    <div class="fw-bold mb-2">
                        Add end distance for
                        <?php echo formatDate($latestOdometerRecord['date']) ?>
                    </div>

                    <div class="fw-light">
                        Started on
                        <?php echo $latestOdometerRecord['start_distance'] ?>
                        kms
                    </div>

                    <?php if (isset($previousOdometerRecord)) { ?>
                        <div class="fw-light mb-2"> Previous day (<?php echo formatDateShort( $previousOdometerRecord['date']) ?>) trip:
                            <?php echo round(($previousOdometerRecord['end_distance'] - $previousOdometerRecord['start_distance']), 2) ?>
                            kms
                        </div>
                    <?php } ?>

                    <input type="hidden" name="id" value="<?php echo $latestOdometerRecord['id']; ?>">
                    <input type="hidden" name="vehicle" value="<?php echo $latestOdometerRecord['vehicle_id']; ?>">
                    <input type="date" id="date" name="date" class="form-control" value="<?php echo htmlspecialchars($latestOdometerRecord['date']); ?>" required hidden>
                    <input type="hidden" id="start_distance" name="start_distance" value="<?php echo htmlspecialchars($latestOdometerRecord['start_distance']); ?>">
                    <div class="form-group">
                        <input type="number" id="end_distance" name="end_distance" class="form-control" step="any" inputmode="decimal" value="<?php echo round($suggestedEndDistance, 2); ?>" required autofocus>
                        <div class="form-text" id="distancePreview"></div>
                    </div>
                    <input type="hidden" name="addingEndDistance" value="1">
                    <div class="form-row d-flex align-items-center mt-2">
                        <div class="form-group flex-grow-1">
                            <textarea id="comment" rows="1" name="comment" placeholder="Any Comments ?" class="form-control"><?php echo htmlspecialchars($latestOdometerRecord['comment']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary ms-2 d-flex align-items-center">
                            Save
                        </button>
                    </div>
                <?php } ?>
            </form>
            <a href="odometer/view.php" class="btn btn-link">
                Manage Odometer
            </a>
        </div>
    </div>
</div>

<script>
    const startDistance = parseFloat(document.getElementById('start_distance')?.value || 0);
    const endDistanceInput = document.getElementById('end_distance');
    const preview = document.getElementById('distancePreview');

    function updateDistancePreview() {
        const endDistance = parseFloat(endDistanceInput.value || 0);

        if (!isNaN(startDistance) && !isNaN(endDistance) && endDistance >= startDistance) {
            const distance = (endDistance - startDistance).toFixed(2);
            preview.innerHTML = `Trip distance: <strong>${distance} km</strong>`;
        } else {
            preview.innerHTML = '';
        }
    }

    if (endDistanceInput) {
        updateDistancePreview();

        endDistanceInput.addEventListener('input', updateDistancePreview);

        endDistanceInput.addEventListener('focus', function () {
            this.select();
        });
    }
</script>

<?php
initializePageFooter($rootPath, $moduleType);
?>