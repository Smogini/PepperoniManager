<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

requireAuth();

$conn = Database::getConnection();

try {
    $veicoloId = (int)$_POST['veicolo_id'];
    $data = $_POST['data'];
    $note = $_POST['note'];

    updateRevisionNote($conn, $veicoloId, $data, $note);

    header("Location: ../pages/manage_vehicles.php?success=1");
    exit;
} catch (Exception $e) {
    header("Location: ../pages/manage_vehicles.php?error=" . urlencode($e->getMessage()));
    exit;
}