<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

startSession();

$conn = Database::getConnection();

try {
    $tipo = $_POST['tipo_veicolo'] ?? null;
    $stato = $_POST['stato_veicolo'] ?? null;
    $prezzo = (float) $_POST['prezzo'] ?? null;
    $data = $_POST['date'] ?? null;
    $note = $_POST['note'] ?? null;

    if (!isset($tipo) || !isset($stato) || !isset($data) || !isset($prezzo)) {
        throw new Exception("Campi mancanti");
    }

    $conn->begin_transaction();

    $vehicleId = insertVehicle($conn, $tipo, $stato);

    if ($vehicleId <= 0) {
        $e = "Errore nell'inserimento del veicolo";
        header("Location: ../pages/insert_vehicle.php?error=" . urlencode($e));
        exit;
    }
    
    if (!insertOverhaul($conn, $vehicleId, $data, $prezzo, $note)) {
        $e = "Errore nell'inserimento della revisione per il veicolo #" . urlencode($vehicleId);
        header("Location: ../pages/manage_deliveries.php?error=" . urlencode($e));
        exit;
    }

    $conn->commit();

    header("Location: ../pages/insert_vehicle.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/insert_vehicle.php?error=" . urlencode($e->getMessage()));
    exit;
}
