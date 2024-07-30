<?php
$rootPath = $_SERVER['DOCUMENT_ROOT'];
require_once $rootPath . '/config/config.php';
$conn = initDb();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Query the database to retrieve booked dates and sessions
    $query = "SELECT appointment_date, session FROM appointments WHERE appointment_date >= CURDATE()";
    $result = $conn->query($query);

    $bookedDates = [];
    $bookedSessions = [];

    while ($row = $result->fetch_assoc()) {
        $bookedDates[] = $row['appointment_date'];
        $bookedSessions[$row['appointment_date']][] = $row['session'];
    }

    $allDates = getAllDates();

    $availableDates = array_diff($allDates, $bookedDates);

    $queryDisabled = "SELECT disabled_date FROM appointments_disabled_dates";
    $resultDisabled = $conn->query($queryDisabled);

    $disabledDates = [];
    while ($rowDisabled = $resultDisabled->fetch_assoc()) {
        $disabledDates[] = $rowDisabled['disabled_date'];
    }
    echo json_encode([
        'available_dates' => $availableDates,
        'booked_dates' => $bookedDates,
        'booked_sessions' => $bookedSessions,
        'disabled_dates' => $disabledDates
    ]);
}

function getAllDates() {
    // Implement a function to generate all possible dates based on your requirements
    // For example, you can generate dates for the next 30 days.
    $allDates = [];
    $currentDate = new DateTime();
    for ($i = 0; $i < 30; $i++) {
        $allDates[] = $currentDate->format('Y-m-d');
        $currentDate->add(new DateInterval('P1D'));
    }
    return $allDates;
}
