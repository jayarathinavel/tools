<?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/config/config.php';
    initializePage("Add Mileage Record", "main", $_SERVER['REQUEST_URI']);
    includePhpFileFromRoot($rootPath, '/pages/tools/vehicle-tracker/vehicle-tracker-utils.php');
    $userId = vehicleTrackerUser();
    $vehicle = findVehicleForUser($userId);

    if (isset($_POST['date'])) {
        includePhpFileFromRoot($rootPath, '/handlers/tools/vehicle-tracker-mileage-handler.php');
    }

    $vehicles = fetchVehicles($userId);
?>

<div class="container">
    <h1>Add Mileage Record</h1>
    <form action="" method="post" id="add-mileage-record">
        <div class="fw-bold">
            <?php
                foreach ($vehicles as $id => $name):
                    if($id == intval($vehicle)) {
                        echo 'Add New Mileage Record to ' . $name;
                    }
                endforeach;
            ?>
        </div>
        <div class="form-group">
            <label for="date">Date:</label>
            <input type="date" id="date" name="date" class="form-control" required>
        </div>
 
        <label for="fuel_quantity">Fuel State:</label>

        <div class="border 0 p-2">
            <div class="form-group mb-2">
                <input type="number" id="fuel_quantity" name="fuel_quantity" class="form-control" placeholder="Enter fuel quantity">
            </div>
            <h6 class="text-center">(or)</h6>
            <div class="form-group">
                <label class="form-check-label">
                    <input type="checkbox" id="fuel_empty" name="fuel_empty" class="form-check-input">
                    Fuel Empty
                </label>
            </div>
        </div>
    
        <input type="hidden" id="fuel_state" name="fuel_state">
        <div class="form-group">
            <label for="odometer_reading">Odometer Reading:</label>
            <div class="input-group">
                <input type="number" id="odometer_reading" name="odometer_reading" class="form-control" step="any" required>
                <input type="number" id="trip" name="trip" class="form-control ms-2" step="any" value="0" placeholder="Trip (default 0)">
            </div>
        </div>
        <div class="form-group">
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" class="form-control"></textarea>
        </div>
        <input type="submit" value="Add Record" class="btn btn-primary mt-2">
    </form>
</div>

<?php
    initializePageFooter($rootPath, $moduleType);
    setTodaysDateForForm();
?>

<script>

    const odometerInput = document.getElementById('odometer_reading');
    const tripInput = document.getElementById('trip');

    document.getElementById('add-mileage-record').addEventListener('submit', function(event) {
        const odometerValue = parseFloat(odometerInput.value) || 0;
        const tripValue = parseFloat(tripInput.value) || 0;

        if (tripValue !== 0) {
            odometerInput.value = odometerValue - tripValue;
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.getElementById('fuel_quantity');
        const emptyCheckbox = document.getElementById('fuel_empty');
        const fuelStateInput = document.getElementById('fuel_state');

        document.getElementById('add-mileage-record').addEventListener('submit', function(event) {
            if (emptyCheckbox.checked && quantityInput.value) {
                alert('Please select either fuel quantity or fuel empty, not both.');
                event.preventDefault(); // Prevent form submission
            } else {
                // Set the value of the hidden input based on the selected option
                if (emptyCheckbox.checked) {
                    fuelStateInput.value = 'Fuel Empty';
                } else if (quantityInput.value) {
                    fuelStateInput.value = quantityInput.value;
                } else {
                    alert('Please enter a fuel quantity or check "Fuel Empty".');
                    event.preventDefault(); // Prevent form submission
                }
            }
        });

        quantityInput.addEventListener('input', function() {
            emptyCheckbox.checked = false;
        });

        emptyCheckbox.addEventListener('change', function() {
            if (this.checked) {
                quantityInput.value = '';
            }
        });
    });
</script>
