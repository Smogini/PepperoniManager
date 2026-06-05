<?php
function userExists(mysqli $conn, string $username): bool {
    $query = "
        SELECT persona_id
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
            persona_id,
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

function getAllUsers(mysqli $conn): array {
    return $conn->execute_query("
        SELECT
            u.persona_id AS utente_id,
            u.*,

            p.nome,
            p.cognome

    FROM UTENTE u
    INNER JOIN PERSONA p
    ON u.persona_id = p.id

    ORDER BY p.cognome, p.nome
    ")->fetch_all(MYSQLI_ASSOC);
}

function changeUserStatus(mysqli $conn, int $personaId, string $newState): bool {
    if ($personaId < 0 || !isset($newState)) {
        return false;
    }

    $query = "
        UPDATE UTENTE
        SET stato = ?
        WHERE persona_id = ?
    ";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $newState, $personaId);

    return $stmt->execute();
}
