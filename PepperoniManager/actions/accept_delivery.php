<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/element_status.php';

requireAuth();

$conn = Database::getConnection();

try {
    $courierId = $_SESSION['id'];
    $deliveryId = (int)$_POST['consegna_id'];
    $deliveryStatus = Delivery_Status::map();

    $query = "
        UPDATE consegna
        SET fattorino_id = ?, stato = ?
        WHERE id = ?
        AND stato = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isis", $courierId, $deliveryStatus['IN_CONSEGNA'], $deliveryId, $deliveryStatus['CREATA']);
    $stmt->execute();

    header("Location: ../pages/view_delivery.php");
    exit;
} catch(Exception $e) {
    header("Location: ../pages/view_delivery.php?error=" . urlencode($e->getMessage()));
    exit;
}