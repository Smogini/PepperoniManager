<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/ingredients.php';

requireAuth();

$conn = Database::getConnection();

try {
    $nome = $_POST['nome'] ?? null;
    $prezzo = (float) $_POST['prezzo'] ?? null;
    $descrizione = $_POST['descrizione'] ?? null;
    
    $conn->begin_transaction();

    if (insertIngredient($conn, $nome, $prezzo, $descrizione)) {
        $e = "Errore nell'inserimento dell'ingrediente";
        header("Location: ../pages/insert_ingredient.php?error=" . urlencode($e));
        exit;
    }

    $conn->commit();

    header("Location: ../pages/insert_ingredient.php?success=1");
    exit();
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/insert_ingredient.php?error=" . urlencode($e->getMessage()));
    exit;
}