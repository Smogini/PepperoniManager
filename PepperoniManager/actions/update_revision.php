<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

requireAuth();

$conn = Database::getConnection();

try {
    $veicoloId = (int) $_POST['veicolo_id'] ?? null;
    $data = $_POST['data'] ?? null;
    $note = $_POST['note'] ?? null;

    if (!isset($veicoloId) || !isset($data)) {
        throw new Exception("Campi mancanti");
    }

    $conn->begin_transaction();

    updateRevisionNote($conn, $veicoloId, $data, $note);

    $conn->commit();

    header("Location: ../pages/manage_vehicles.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_vehicles.php?error=" . urlencode($e->getMessage()));
    exit;
}