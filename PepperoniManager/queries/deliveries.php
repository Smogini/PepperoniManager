<?php
require_once '../config/element_status.php';

function createDelivery(
    mysqli $conn,
    int $orderId,
    string $via,
    string $nCivico,
    string $citta,
    string $cap
): bool {

    $deliveryStatus = Delivery_Status::CREATA->value;
    $query = "
        INSERT INTO CONSEGNA (
            ordine_id,
            via,
            n_civico,
            citta,
            cap,
            stato
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);

    $stmt->bind_param(
        "isssss",
        $orderId,
        $via,
        $nCivico,
        $citta,
        $cap,
        $deliveryStatus
    );

    return $stmt->execute();
}

function getAvailableDeliveries(mysqli $conn, string $created, string $waiting): array {
    $query = "
        SELECT 
            c.id AS consegna_id,
            o.id AS ordine_id,
            o.data,
            o.tipo_acquisizione,
            c.via,
            c.n_civico,
            c.citta,
            c.cap,
            c.stato
        FROM consegna c
        JOIN ordine o ON o.id = c.ordine_id
        WHERE c.fattorino_id IS NULL
          AND (c.stato = ? OR c.stato = ?)
        ORDER BY o.data ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $created, $waiting);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getDeliveriesByCourierId(mysqli $conn, int $courierId): array {
    $query = "
        SELECT 
            c.id AS consegna_id,
            o.id AS ordine_id,
            o.data,
            o.tipo_acquisizione,
            c.via,
            c.n_civico,
            c.citta,
            c.cap,
            c.stato
        FROM consegna c
        JOIN ordine o ON o.id = c.ordine_id
        WHERE c.fattorino_id = ?
        ORDER BY o.data DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $courierId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function acceptDelivery(mysqli $conn, int $deliveryId, int $courierId): bool {
    $query = "
        UPDATE CONSEGNA
        SET fattorino_id = ?, stato = ?
        WHERE id = ?
        AND stato = ?
    ";

    $shippingStatus = Delivery_Status::IN_CONSEGNA->value;
    $createdStatus = Delivery_Status::CREATA->value;

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isis", $courierId, $shippingStatus, $deliveryId, $createdStatus);
    
    return $stmt->execute();
}

function holdDelivery(mysqli $conn, int $orderId): bool {
    $query = "
        UPDATE consegna
        SET stato = ?
        WHERE ordine_id = ?
    ";

    $holdStatus = Delivery_Status::IN_ATTESA->value;

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $holdStatus, $orderId);
    
    return $stmt->execute();
}

function completeDelivery(mysqli $conn, int $deliveryId): bool {
    $updateDelivery = "
        UPDATE CONSEGNA
        SET stato = ?
        WHERE id = ?
        AND stato = ?
    ";

    $shippedStatus = Delivery_Status::CONSEGNATA->value;
    $shippingStatus = Delivery_Status::IN_CONSEGNA->value;

    $stmt = $conn->prepare($updateDelivery);
    $stmt->bind_param("sis", $shippedStatus, $deliveryId, $shippingStatus);
    
    return $stmt->execute();
}
