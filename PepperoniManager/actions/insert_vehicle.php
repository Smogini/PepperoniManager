<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

requireAuth();

$conn = Database::getConnection();

try {
    $tipo = $_POST['tipo_veicolo'] ?? '';
    $stato = $_POST['stato_veicolo'] ?? '';

    if ($tipo === '' || $stato === '') {
        throw new Exception("Seleziona un'opzione valida");
    }

    $conn->begin_transaction();

    $vehicleId = insertVehicle($conn, $tipo, $stato);

    if (!empty($_POST['prezzo'])) {
        $data = $_POST['date'] ?? null;
        $prezzo = (float) $_POST['prezzo'];
        $note = $_POST['note'] ?? '';

        insertOverhaul($conn, $vehicleId, $data, $prezzo, $note);
    }

    $conn->commit();

    header("Location: ../pages/insert_vehicle.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/insert_vehicle.php?error=" . urlencode($e->getMessage()));
    exit;
}