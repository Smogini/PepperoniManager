<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/users.php';

startSession();

$conn = Database::getConnection();

try {
    $userId = $_POST['id_persona'];
    $newState = $_POST['new_state'];

    $conn->begin_transaction();

    if (!changeUserStatus($conn, $userId, $newState)) {
        throw new Exception("Errore durante il cambio di stato dell'utente");
    }

    $conn->commit();

    header("Location: ../pages/manage_users.php?success=" . urlencode("Utente " . $newState . " con successo"));
    exit;
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/manage_users.php?error=" . urlencode($e->getMessage()));
    exit;
}