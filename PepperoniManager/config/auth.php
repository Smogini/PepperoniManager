<?php
function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function requireAuth(string $userRole, ?string $employeeRole = null): void {
    startSession();

    if (!isset($_SESSION['id']) | (!isset($_SESSION['role']) || $_SESSION['role'] !== $userRole)) {
        destroySession(true);
    }

    if (isset($employeeRole) && $_SESSION['employee_role'] !== $employeeRole) {
        destroySession(true);
    }
}

function destroySession(bool $destroy = false) {
    if ($destroy) {
        session_destroy();
        header("Location: /PepperoniManager/pages/login.php");
        exit;
    }
}

function getUserRole(): ?string {
    return $_SESSION['role'] ?? null;
}

function getEmployeeRole(): ?string {
    return $_SESSION['employee_role'] ?? null;
}

function getUsername(): ?string {
    return $_SESSION['username'] ?? null;
}
