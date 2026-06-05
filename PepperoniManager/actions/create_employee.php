<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/employees.php';

startSession();

$conn = Database::getConnection();

try {
    $nome = $_POST['nome'] ?? null;
    $cognome = $_POST['cognome'] ?? null;
    $ruolo = $_POST['ruolo'] ?? null;
    $password = $_POST['password'] ?? null;

    if (!isset($ruolo) || !isset($password)) {
        throw new Exception("Compila tutti i campi");
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    $personId = insertPerson($conn, $nome, $cognome);

    if (!insertEmployee($conn, $personId, $ruolo, $hash)) {
        $e = "Errore nell'inserimento del dipendente";
        header("Location: ../pages/register_employee.php?error=" . urlencode($e));
        exit;
    }

    $conn->commit();

    header("Location: ../pages/register_employee.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/register_employee.php?error=" . urlencode($e->getMessage()));
    exit;
}
