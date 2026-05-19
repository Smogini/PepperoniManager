<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

startSession();

$conn = Database::getConnection();

try {
    $veicoloId = (int) $_POST['veicolo_id'];
    $data = $_POST['data'];
    $prezzo = (float) $_POST['prezzo'];
    $note = $_POST['note'] ?? '';

    $conn->begin_transaction();

    insertOverhaul($conn, $veicoloId, $data, $prezzo, $note);

    $conn->commit();

    header("Location: ../pages/manage_vehicles.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_vehicles.php?error=" . urlencode($e->getMessage()));
    exit;
}
