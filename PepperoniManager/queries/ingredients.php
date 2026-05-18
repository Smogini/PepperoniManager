<?php
function insertIngredient(mysqli $conn, string $nome, float $prezzo, string $descrizione): bool {
    $query = "
        INSERT INTO INGREDIENTE (
            nome,
            prezzo,
            descrizione
        )
        VALUES (?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sds", $nome, $prezzo, $descrizione);
    return $stmt->execute();
}

function getAllIngredients(mysqli $conn): array {
    return $conn->execute_query("SELECT * FROM INGREDIENTE")->fetch_all(MYSQLI_ASSOC);
}

function calculateIngredientsPrice(mysqli $conn, array $ids): float {
    if (empty($ids)) return 0;

    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $query = "
        SELECT SUM(prezzo) AS totale
        FROM ingrediente
        WHERE id IN ($placeholders)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    return (float) ($result['totale'] ?? 0);
}
