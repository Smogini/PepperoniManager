<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';
require_once '../queries/deliveries.php';
require_once '../queries/pizzas.php';

startSession();

$conn = Database::getConnection();

try {
    $userId = $_SESSION['id'] ?? null;
    $tipo = $_POST['tipo_acquisizione'];
    $pizze = $_POST['pizze'] ?? [];

    if (empty($tipo)) {
        throw new Exception("Tipo ordine mancante");
    }

    if (empty($pizze)) {
        throw new Exception("Seleziona almeno una pizza");
    }

    $conn->begin_transaction();

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
        if (!createDelivery(
            $conn,
            $orderId,
            $_POST['via'],
            $_POST['n_civico'],
            $_POST['citta'],
            $_POST['cap']
        )) {
            throw new Exception("Errore nell'inserimento della consegna per l'ordine #" . urlencode($orderId));
        }
    }

    $conn->commit();

    header("Location: ../pages/create_order.php?success=1");
    exit;
} catch(Exception $e) {
    $conn->rollback();
    header("Location: ../pages/create_order.php?error=" . urlencode($e->getMessage()));
    exit;
}
