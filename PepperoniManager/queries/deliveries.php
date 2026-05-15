<?php
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