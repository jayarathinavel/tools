<?php
    $rootPath = $_SERVER['DOCUMENT_ROOT'];
    require_once $rootPath . '/config/config.php';
    $pdo = initDbPdo();

    $data = json_decode(file_get_contents("php://input"), true);
    $appointmentId = $data['appointmentId'];
    $action = $data['action'];
    $statusMessage = $data['statusMessage'];

    try {

        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("UPDATE appointments SET status = :status, status_message = :status_message WHERE id = :appointment_id");
        $stmt->bindParam(':status', $action);
        $stmt->bindParam(':status_message', $statusMessage);
        $stmt->bindParam(':appointment_id', $appointmentId);
        $stmt->execute();
        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
