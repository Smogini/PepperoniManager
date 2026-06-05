<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/ingredients.php';
require_once '../queries/pizzas.php';

startSession();

$conn = Database::getConnection();

try {
    $nome = $_POST['nome'] ?? null;
    $descrizione = $_POST['descrizione'] ?? null;
    $basePrice = (float) $_POST['prezzo'] ?? null;
    $disponibilita = isset($_POST['disponibilita']) ? 1 : 0;
    $ingredienti = $_POST['ingredienti'] ?? null;

    $conn->begin_transaction();

    $extra = calculateIngredientsPrice($conn, $ingredienti);
    $finalPrice = $basePrice + $extra;

    $pizzaId = insertPizza(
        $conn,
        $nome,
        $descrizione,
        $finalPrice,
        $disponibilita
    );

    if (!insertRecipe($conn, $pizzaId, $ingredienti)) {
        throw new Exception("Errore nell'inserimento della ricetta");
    }

    $conn->commit();

    header("Location: ../pages/insert_pizza.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/insert_pizza.php?error=" . urlencode($e->getMessage()));
    exit;
}
