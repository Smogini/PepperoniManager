<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/employees.php';

requireAuth();

$conn = Database::getConnection();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: ../pages/dashboard.php");
    exit;
}

try {
    $nome = $_POST['nome'] ?? '';
    $cognome = $_POST['cognome'] ?? '';
    $ruolo = $_POST['ruolo'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($ruolo === '' || $password === '') {
        throw new Exception("Compila tutti i campi");
    }

    // $hash = password_hash($password, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    $personId = insertPerson($conn, $nome, $cognome);

    // insertEmployee($conn, $personId, $ruolo, $hash);
    if (!insertEmployee($conn, $personId, $ruolo, $password)) {
        throw new Exception("Insert dipendente fallito");
    }

    $conn->commit();

    header("Location: ../pages/register_employee.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/register_employee.php?error=" . urlencode($e->getMessage()));
    exit;
}