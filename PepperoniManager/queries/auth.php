<?php
function findUserByUsername(mysqli $conn, string $username): ?array {
    $query = "
        SELECT *
        FROM utente
        WHERE username = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();

    return $result->num_rows === 1 ? $result->fetch_assoc() : null;
}

function findEmployeeByName(mysqli $conn, string $name): ?array {
    $query = "
        SELECT p.id, p.nome, d.ruolo, d.password
        FROM persona p
        JOIN dipendente d ON p.id = d.id
        WHERE p.nome = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);
    $stmt->execute();

    $result = $stmt->get_result();

    return $result->num_rows === 1 ? $result->fetch_assoc() : null;
}