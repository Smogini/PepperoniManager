<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

startSession();

$conn = Database::getConnection();

try {
    $id = (int) $_POST['veicolo_id'] ?? null;
    $stato = $_POST['stato_operativo'] ?? null;

    if (!isset($id) || !isset($stato)) {
        throw new Exception("Campi mancanti");
    }

    $conn->begin_transaction();

    updateVehicleStatus($conn, $id, $stato);

    $conn->commit();

    header("Location: ../pages/manage_vehicles.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_vehicles.php?error=" . urlencode($e->getMessage()));
    exit;
}
