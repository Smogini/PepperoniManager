<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/reviews.php';

startSession();

$conn = Database::getConnection();

try {
    $recensioneId = intval($_POST['recensione_id']);
    $nuovoStato = $_POST['nuovo_stato'];

    $conn->begin_transaction();

    if (!changeReviewStatus($conn, $recensioneId, $nuovoStato)) {
        throw new Exception("Errore durante il cambio di stato dell'utente");
    }

    $conn->commit();

    header("Location: ../pages/manage_users.php?success=" . urlencode("Recensione " . $nuovoStato . " con successo"));
    exit;
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_users.php?error=" . urlencode($e->getMessage()));
    exit;
}