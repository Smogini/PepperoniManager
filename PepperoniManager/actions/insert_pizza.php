<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/pizza.php';

requireAuth();

$conn = Database::getConnection();

try {
    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'] ?? '';
    $basePrice = (float) $_POST['prezzo'];
    $disponibilita = isset($_POST['disponibilita']) ? 1 : 0;
    $ingredienti = $_POST['ingredienti'] ?? [];

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

    insertRecipe($conn, $pizzaId, $ingredienti);

    $conn->commit();

    header("Location: ../pages/insert_pizza.php?success=1");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/insert_pizza.php?error=" . urlencode($e->getMessage()));
    exit;
}