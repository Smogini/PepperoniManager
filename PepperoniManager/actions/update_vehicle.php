<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

requireAuth();

$conn = Database::getConnection();

try {
    $id = (int)$_POST['veicolo_id'];
    $stato = $_POST['stato_operativo'];

    updateVehicleStatus($conn, $id, $stato);

    header("Location: ../pages/manage_vehicles.php?success=1");
    exit;
} catch (Exception $e) {
    header("Location: ../pages/manage_vehicles.php?error=" . urlencode($e->getMessage()));
    exit;
}