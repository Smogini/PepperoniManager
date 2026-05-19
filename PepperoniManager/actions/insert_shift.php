<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/shifts.php';

startSession();

$conn = Database::getConnection();

try {
    $data = $_POST['data'];
    $ora_inizio = $_POST['ora_inizio'];
    $ora_fine = $_POST['ora_fine'];
    $dipendentiSelezionati = $_POST['dipendenti'] ?? [];
    $veicoliDipendenti = [];
    
    if ($ora_inizio >= $ora_fine) {
        throw new Exception("Inserisci un orario valido!");
    }

    $conn->begin_transaction();

    foreach ($dipendentiSelezionati as $dipendente_id) {
        $chiaveVeicolo = "veicolo_" . $dipendente_id;

        $veicoliDipendenti[$dipendente_id] = $_POST[$chiaveVeicolo] ?? null;
    }

    insertShift($conn, $data, $ora_inizio, $ora_fine, $dipendentiSelezionati, $veicoliDipendenti);

    $conn->commit();

    header("Location: ../pages/insert_shift.php?success=1");
    exit();
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/insert_shift.php?error=" . urlencode($e->getMessage()));
    exit();
}
