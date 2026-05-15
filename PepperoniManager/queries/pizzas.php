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
): void {

    $query = "
        INSERT INTO ricetta (pizza_id, ingrediente_id)
        VALUES (?, ?)
    ";

    $stmt = $conn->prepare($query);

    foreach ($ingredienti as $id) {
        $stmt->bind_param("ii", $pizzaId, $id);
        $stmt->execute();
    }
}