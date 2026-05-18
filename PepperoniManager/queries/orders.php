<?php
require_once '../config/element_status.php';

function getAllOrders(mysqli $conn): array {
    $query = "
        SELECT *
        FROM ordine
        ORDER BY data DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getUserOrders(mysqli $conn, int $userId): array {
    $query = "
        SELECT
            O.id,
            O.data,
            O.stato,
            O.tipo_acquisizione,
            COALESCE(SUM(DO.prezzo_finale), 0) AS totale,
            R.voto,
            R.commento,
            C.stato AS stato_consegna
        FROM ordine O
        LEFT JOIN dettaglio_ordine DO ON O.id = DO.ordine_id
        LEFT JOIN recensione R ON O.id = R.ordine_id
        LEFT JOIN consegna C ON O.id = C.ordine_id
        WHERE O.utente_id = ?
        GROUP BY
            O.id,
            O.data,
            O.stato,
            O.tipo_acquisizione,
            R.voto,
            R.commento,
            C.stato
        ORDER BY O.data DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function acceptOrder(mysqli $conn, int $orderId): bool {
    $query = "
        UPDATE ORDINE
        SET stato = ?
        WHERE id = ?
    ";
    
    $acceptedStatus = Order_Status::ACCETTATO->value;

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $acceptedStatus, $orderId);

    return $stmt->execute();
}

function completeOrder(mysqli $conn, int $orderId): bool {
    $query = "
        UPDATE ORDINE
        SET stato = ?
        WHERE id = ?
    ";

    $completedStatus = Order_Status::COMPLETATO->value;

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $completedStatus, $orderId);
    return $stmt->execute();
}

function createOrder(mysqli $conn, int $userId, string $tipoAcquisizione): int {
    $waitingStatus = Order_Status::IN_ATTESA->value;
    $query = "
        INSERT INTO ORDINE (
            utente_id,
            data,
            stato,
            tipo_acquisizione
        )
        VALUES (?, NOW(), ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("iss", $userId, $waitingStatus, $tipoAcquisizione);
    $stmt->execute();

    return $conn->insert_id;
}

function addPizzaToOrder(
    mysqli $conn,
    int $orderId,
    int $pizzaId,
    float $prezzo,
    ?string $note = null
): bool {

    $query = "
        INSERT INTO DETTAGLIO_ORDINE (
            ordine_id,
            pizza_id,
            prezzo_finale,
            note
        )
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);

    $stmt->bind_param(
        "iids",
        $orderId,
        $pizzaId,
        $prezzo,
        $note
    );

    return $stmt->execute();
}

function getOrderDetails(mysqli $conn, int $orderId): array {
    $query = "
        SELECT
            P.nome,
            DO.prezzo_finale
        FROM dettaglio_ordine DO
        INNER JOIN pizza P ON DO.pizza_id = P.id
        WHERE DO.ordine_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
