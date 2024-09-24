<?php
    function vehicleTrackerUser(){
        return 1;
    }

    function findVehicleForUser($userId){
        $selectedVehicle = null;
        if(isset($_SESSION['vehicleTrackerSelectedVehicle'])){
            $selectedVehicle = $_SESSION['vehicleTrackerSelectedVehicle'];
        } else{
            $conn = initDb();
            $vehicles = $conn->query("SELECT * FROM vehicles WHERE user_id = $userId");
            $conn->close();
            if($vehicles->num_rows > 0){
                $selectedVehicle = $vehicles->fetch_assoc()["id"];
                $_SESSION['vehicleTrackerSelectedVehicle'] = $selectedVehicle;
            }
        }
        return $selectedVehicle;
    }

    function clearSelectedVehicle(){
        if(isset($_SESSION['vehicleTrackerSelectedVehicle'])) {
            unset($_SESSION['vehicleTrackerSelectedVehicle']);
        }
        
    }

    function fetchVehicleDetails($vehicleId){
        if(isset($vehicleId)){
            $conn = initDb();
            $query = "SELECT * FROM vehicles WHERE id=$vehicleId";
            return $conn->query($query)->fetch_assoc();
        }
    }

    function displayVehicleDetails($vehicleId) {
        $vehicleDetails = fetchVehicleDetails($vehicleId);
        if(isset($vehicleDetails)) {
            echo "
                <div>
                    <h5>Vehicle :  " . $vehicleDetails['name'] . "</h5 >
                </div>
            ";
        }
    }

    function fetchVehicles($userId) {
        $conn = initDb();
        $query = "SELECT id, name FROM vehicles WHERE user_id=$userId";
        $result = $conn->query($query);
        if ($result === false) {
            return [];
        }
        $vehicles = [];
        while ($row = $result->fetch_assoc()) {
            $vehicles[$row['id']] = $row['name'];
        }
        return $vehicles;
    }

    function findOdometerRecord($recordId) {
        $conn = initDb();
        $recordId = $conn->real_escape_string($recordId);
        $query = "SELECT * FROM odometer WHERE id = '$recordId'";
        $result = $conn->query($query);
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return null;
        }
    }

    function fetchMileageRecords($vehicleId) {
        if(isset($vehicleId)){
            global $conn;
            $vehicleId = $conn->real_escape_string($vehicleId);
            $query = "SELECT * FROM mileage WHERE vehicle_id = $vehicleId ORDER BY date DESC;";
            $result = $conn->query($query);
            $mileageRecords = [];
            while ($row = $result->fetch_assoc()) {
                $mileageRecords[] = $row;
            }
            return $mileageRecords;
        }
    }

    function findMileageRecord($id) {
        global $conn;
        $id = $conn->real_escape_string($id);
        $query = "SELECT * FROM mileage WHERE id=$id";
        $result = $conn->query($query);
        return $result->fetch_assoc();
    }

    function fetchLatestOdometerRecord($vehicleId){
        $conn = initDb();
        $sql = "SELECT * FROM odometer WHERE vehicle_id = $vehicleId ORDER BY id DESC LIMIT 1";
        $result = $conn->query($sql);
        return  $result->fetch_assoc();
    }

    //fetchLatestMileageAndFuelQuantity
    {
        function fetchLatestMileageAndFuelQuantity($vehicleId) {
            $mileageRecords = fetchMileageRecords($vehicleId);
            $mileageRecords = array_slice($mileageRecords, 0, 10);
            $latestFuelData = extractLatestFuelData($mileageRecords);
            $calculatedMileage = calculateMileage($latestFuelData['latestFuelEmpty'], $latestFuelData['secondLatestFuelEmpty'], $latestFuelData['fuelQuantity']);
        
            if ($calculatedMileage !== null) {
                $latestFuelData['latestFuelEmpty']["calculated_mileage"] = $calculatedMileage;
            }
        
            return [
                'latest_fuel_quantity' => $latestFuelData['latestFuelQuantityRecord'],
                'latest_fuel_empty' => $latestFuelData['latestFuelEmpty'],
            ];
        }
        
        function extractLatestFuelData($mileageRecords) {
            $latestFuelEmpty = [];
            $secondLatestFuelEmpty = [];
            $fuelQuantity = 0;
            $latestFuelQuantityRecord = [];
        
            foreach ($mileageRecords as $record) {
                if ($record["fuel_state"] == "Fuel Empty") {
                    if (empty($latestFuelEmpty)) {
                        $latestFuelEmpty = $record;
                    } elseif (empty($secondLatestFuelEmpty)) {
                        $secondLatestFuelEmpty = $record;
                    }
                } else {
                    if (empty($latestFuelQuantityRecord)) {
                        $latestFuelQuantityRecord = $record; // Update latest fuel quantity record
                    }
                    if (!empty($latestFuelEmpty) && empty($secondLatestFuelEmpty)) {
                        $fuelQuantity += doubleval($record["fuel_state"]); // Sum the fuel quantities
                    }
                }
            }
        
            return [
                'latestFuelEmpty' => $latestFuelEmpty,
                'secondLatestFuelEmpty' => $secondLatestFuelEmpty,
                'fuelQuantity' => $fuelQuantity,
                'latestFuelQuantityRecord' => $latestFuelQuantityRecord,
            ];
        }
        
        function calculateMileage($latestFuelEmpty, $secondLatestFuelEmpty, $fuelQuantity) {
            if (!empty($latestFuelEmpty) && !empty($secondLatestFuelEmpty) && $fuelQuantity > 0) {
                $latestOdometer = $latestFuelEmpty["odometer_reading"];
                $secondLatestOdometer = $secondLatestFuelEmpty["odometer_reading"];
                return ($latestOdometer - $secondLatestOdometer) / $fuelQuantity;
            }
            return null;
        }
        
    }
    
    function fetchMaintenanceRecords($vehicleId) {
        if (isset($vehicleId)) {
            global $conn;
            $vehicleId = $conn->real_escape_string($vehicleId);
            $query = "SELECT * FROM vt_maintenance  WHERE vehicle_id = $vehicleId ORDER BY date DESC;";
            $result = $conn->query($query);
            $maintenanceRecords = [];
            while ($row = $result->fetch_assoc()) {
                $maintenanceRecords[] = $row;
            }
            return $maintenanceRecords;
        }
    }

    function findMaintenanceRecord($id) {
        global $conn;
        $id = $conn->real_escape_string($id);
        $query = "SELECT * FROM vt_maintenance  WHERE id = $id";
        $result = $conn->query($query);
        return $result->fetch_assoc();
    }
    
    function fetchNotes($vehicle) {
        $conn = initDb();
        $query = "SELECT id, date, note FROM notes WHERE vehicle_id=$vehicle";
        $result = $conn->query($query);
        if ($result === false) {
            return [];
        }
        $notes = [];
        while ($row = $result->fetch_assoc()) {
            $notes[] = $row;
        }
        return $notes;
    }
    