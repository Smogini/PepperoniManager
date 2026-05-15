<?php
function insertPerson(mysqli $conn, string $nome, string $cognome): int {
    $query = "
        INSERT INTO persona (nome, cognome)
        VALUES (?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $nome, $cognome);
    $stmt->execute();

    return $conn->insert_id;
}

function insertEmployee(
    mysqli $conn,
    int $personId,
    string $ruolo,
    string $passwordHash
): bool {

    $query = "
        INSERT INTO dipendente (id, ruolo, password)
        VALUES (?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $personId, $ruolo, $passwordHash);

    return $stmt->execute();
}