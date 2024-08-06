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
            $books = $conn->query("SELECT * FROM vehicles WHERE user_id = $userId");
            $conn->close();
            if($books->num_rows > 0){
                $selectedVehicle = $books->fetch_assoc()["id"];
                $_SESSION['vehicleTrackerSelectedVehicle'] = $selectedVehicle;
            }
        }
        return $selectedVehicle;
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

    function findMileageRecord($recordId) {
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
    