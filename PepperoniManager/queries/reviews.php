<?php
require_once '../config/element_status.php';

function reviewExists(mysqli $conn, int $orderId): bool {
    $sql = "
        SELECT id
        FROM recensione
        WHERE ordine_id = ?
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    return $stmt->get_result()->num_rows > 0;
}

function insertReview(
    mysqli $conn,
    int $orderId,
    int $userId,
    int $vote,
    string $comment
): bool {
    $publishedStatus = Review_Status::PUBBLICATA->value;
    $sql = "
        INSERT INTO recensione (
            ordine_id,
            utente_id,
            data_pubblicazione,
            voto,
            commento,
            stato
        )
        VALUES (?, ?, NOW(), ?, ?, ?)
    ";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param(
        "iiiss",
        $orderId,
        $userId,
        $vote,
        $comment,
        $publishedStatus
    );

    return $stmt->execute();
}