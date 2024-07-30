<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/config/config.php';
$conn = initDb();
$successMessage = "";
$failureMessage = "";
$appointmentDone = false;
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $appointment_date = $_POST["appointment_date"];
        $name = $_POST["name"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];
        $company_details = $_POST["company_details"];
        $session = $_POST["session"];

        $stmt = $conn->prepare("INSERT INTO appointments
                                    (appointment_date, name, phone, email, company_details, session, created_at)
                                        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP)");
        $stmt->bind_param("ssssss", $appointment_date, $name, $phone, $email, $company_details, $session);
    
        if ($stmt->execute()) {
            $appointmentDone = true;
            $successMessage = "Appointment Booked Sucessfully";
        } else {
            $failureMessage = "Failed to book an appointment";
        }
        $stmt->close();
        $conn->close();
    }
}catch(Exception $e) {
    $failureMessage = $e -> getMessage();
}

