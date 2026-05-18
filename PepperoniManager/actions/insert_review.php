<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/reviews.php';

requireAuth();

$conn = Database::getConnection();

try {
    $orderId = (int)$_POST['ordine_id'];
    $vote = (int)$_POST['voto'];
    $comment = trim($_POST['commento']);
    $userId = $_SESSION['id'];

    if (reviewExists($conn, $orderId)) {
        throw new Exception("Recensione già presente");
    }

    $conn->begin_transaction();

    insertReview($conn, $orderId, $userId, $vote, $comment);

    $conn->commit();

    header("Location: ../pages/view_orders.php");
    exit;
} catch (Exception $e) {
    $conn->rollback();
    header("Location: ../pages/view_orders.php?error=" . urlencode($e->getMessage()));
    exit;
}