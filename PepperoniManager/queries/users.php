<?php
function userExists(mysqli $conn, string $username): bool {
    $query = "
        SELECT id
        FROM utente
        WHERE username = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();

    return $stmt->get_result()->num_rows > 0;
}

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

function insertUser(
    mysqli $conn,
    int $personaId,
    string $email,
    string $username,
    string $passwordHash,
    string $stato,
    string $data
): bool {

    $query = "
        INSERT INTO utente (
            id_persona,
            email,
            username,
            password,
            stato,
            data_registrazione
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(
        "isssss",
        $personaId,
        $email,
        $username,
        $passwordHash,
        $stato,
        $data
    );

    return $stmt->execute();
}

function gettAllUsers(mysqli $conn): array {
    return $conn->execute_query("
        SELECT
            u.id_persona AS utente_id,
            u.*,

            p.nome,
            p.cognome

    FROM UTENTE u
    INNER JOIN PERSONA p
    ON u.id_persona = p.id

    ORDER BY p.cognome, p.nome
    ")->fetch_all(MYSQLI_ASSOC);
}
