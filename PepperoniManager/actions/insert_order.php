<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';
require_once '../queries/pizzas.php';

requireAuth();

$conn = Database::getConnection();

try {
    $conn->begin_transaction();

    $userId = $_SESSION['id'];

    $tipo = $_POST['tipo_acquisizione'];
    $pizze = $_POST['pizze'] ?? [];

    if (empty($tipo)) {
        throw new Exception("Tipo ordine mancante");
    }

    if (count($pizze) === 0) {
        throw new Exception("Seleziona almeno una pizza");
    }

    $orderId = createOrder(
        $conn,
        $userId,
        $tipo
    );

    foreach ($pizze as $pizzaId) {
        $prezzo = getPizzaPrice($conn, $pizzaId);

        addPizzaToOrder(
            $conn,
            $orderId,
            $pizzaId,
            $prezzo
        );
    }

    if ($tipo === "DOMICILIO") {
        createDelivery(
            $conn,
            $orderId,
            $_POST['via'],
            $_POST['n_civico'],
            $_POST['citta'],
            $_POST['cap']
        );
    }

    $conn->commit();

    header("Location: ../pages/create_order.php?success=1");
    exit;
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/create_order.php?error=" . urlencode($e->getMessage()));
    exit;
}