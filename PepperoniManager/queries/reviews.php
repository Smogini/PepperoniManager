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

function getUserReviews(mysqli $conn, int $userId): array {
    $query = "
        SELECT
            r.*

        FROM RECENSIONE r
        INNER JOIN ORDINE o
        ON r.ordine_id = o.id

        WHERE o.utente_id = ?

        ORDER BY r.data_pubblicazione DESC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $userId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function changeReviewStatus(mysqli $conn, int $reviewId, string $newState): bool {
    $query = "
        UPDATE RECENSIONE
        SET stato = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $newState, $reviewId);

    return $stmt->execute();
}
