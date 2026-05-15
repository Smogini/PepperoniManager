<?php
function requireAuth(string $userRole = ""): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['id'])) {
        header("Location: /PepperoniManager/pages/login.php");
        exit;
    }
}

function getUserRole(): ?string {
    return $_SESSION['role'] ?? null;
}

function getEmployeeRole(): ?string {
    return $_SESSION['employ_role'] ?? null;
}

function getUsername(): ?string {
    return $_SESSION['username'] ?? null;
}