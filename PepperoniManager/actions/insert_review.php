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

    insertReview($conn, $orderId, $userId, $vote, $comment);

    header("Location: ../pages/view_orders.php");
    exit;
} catch (Exception $e) {
    header("Location: ../pages/view_orders.php?error=" . urlencode($e->getMessage()));
    exit;
}