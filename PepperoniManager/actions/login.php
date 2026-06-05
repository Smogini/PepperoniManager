<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/auth.php';

startSession();

$conn = Database::getConnection();

try {
    $error = "Credenziali non valide";
    $mode = $_POST['mode'] ?? null;
    $username = $_POST['username'] ?? null;
    $password = $_POST['password'] ?? null;
    $userFound = false;

    if ($mode === "UTENTE") {
        $user = findUserByUsername($conn, $username);

        if ($user['stato'] === "BLOCCATO") {
            throw new Exception("L'utente è stato bloccato, contattare l'amministratore");
        }

        if ($user) {
            if (!password_verify($password, $user['password'])) {
                throw new Exception($error);
            }

            $_SESSION['role'] = "UTENTE";
            $_SESSION['id'] = $user['id_persona'];
            $_SESSION['username'] = $user['username'];

            $userFound = true;
        }
    }

    if ($mode === "DIPENDENTE") {
        $emp = findEmployeeByName($conn, $username);

        if ($emp) {
            if (!password_verify($password, $emp['password'])) {
                throw new Exception($error);
            }

            $_SESSION['role'] = "DIPENDENTE";
            $_SESSION['id'] = $emp['id'];
            $_SESSION['employee_role'] = $emp['ruolo'];
            $_SESSION['username'] = $emp['nome'];

            $userFound = true;
        }
    }

    if ($mode === "AMMINISTRATORE") {
        if ($username === "admin" && $password === "admin") {
            $_SESSION['role'] = "AMMINISTRATORE";
            $_SESSION['id'] = -1;
            $_SESSION['username'] = "Amministratore";

            $userFound = true;
        }
    }

    if (!$userFound) {
        throw new Exception($error);
    }

    header("Location: ../pages/dashboard.php");
    exit;
} catch (Exception $e) {
    header("Location: ../pages/login.php?error=" . urlencode($e->getMessage()));
    exit;
}
