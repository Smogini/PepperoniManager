<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/users.php';

requireAuth();

$conn = Database::getConnection();

try {
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (userExists($conn, $username)) {
        throw new Exception("Username già esistente");
    }

    $conn->begin_transaction();

    $personaId = insertPerson($conn, $nome, $cognome);

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stato = "ATTIVO";
    date_default_timezone_set('Europe/Rome');
    $data = date("Y-m-d H:i:s");

    insertUser(
        $conn,
        $personaId,
        $email,
        $username,
        $hash,
        $stato,
        $data
    );

    $conn->commit();

    header("Location: ../pages/register_user.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/register_user.php?error=" . urlencode($e->getMessage()));
    exit;
}