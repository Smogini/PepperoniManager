<?php
function getAvailablePizzas(mysqli $conn): array {
    $query = "
        SELECT *
        FROM pizza
        WHERE disponibilita = 1
        ORDER BY nome
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getPizzaPrice(
    mysqli $conn,
    int $pizzaId
): float {

    $query = "
        SELECT prezzo
        FROM pizza
        WHERE id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $pizzaId);
    $stmt->execute();

    $result = $stmt->get_result()->fetch_assoc();

    return (float)$result['prezzo'];
}

function insertPizza(
    mysqli $conn,
    string $nome,
    string $descrizione,
    float $prezzo,
    int $disponibilita
): int {

    $query = "
        INSERT INTO pizza (nome, descrizione, prezzo, disponibilita)
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssdi", $nome, $descrizione, $prezzo, $disponibilita);
    $stmt->execute();

    return $conn->insert_id;
}

function insertRecipe(
    mysqli $conn,
    int $pizzaId,
    array $ingredienti
): bool {

    $query = "
        INSERT INTO ricetta (pizza_id, ingrediente_id)
        VALUES (?, ?)
    ";

    $stmt = $conn->prepare($query);

    foreach ($ingredienti as $id) {
        $stmt->bind_param("ii", $pizzaId, $id);
        if (!$stmt->execute()) {
            return false;
        }
    }

    return true;
}
