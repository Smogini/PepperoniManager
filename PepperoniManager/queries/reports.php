<?php
function getTurnover(mysqli $conn): float {
    $query = "
        SELECT SUM(d.prezzo_finale) AS fatturato
        FROM DETTAGLIO_ORDINE d
    ";

    $res = $conn->execute_query($query)->fetch_assoc();

    return (float) $res['fatturato'];
}

function getBestReviews(mysqli $conn): array {
    return $conn->execute_query("
        SELECT
            u.username,
            r.voto,
            r.commento,
            r.data_pubblicazione,
            r.stato,
            r.ordine_id

        FROM RECENSIONE r
        INNER JOIN UTENTE u
        ON r.utente_id = u.id_persona

        ORDER BY r.voto DESC
    ")->fetch_all(MYSQLI_ASSOC);
}

function getBestPizzas(mysqli $conn): array {
    return $conn->execute_query("
        SELECT
            p.nome,
            COUNT(*) AS totale_ordini

        FROM DETTAGLIO_ORDINE d
        INNER JOIN PIZZA p
        ON d.pizza_id = p.id

        GROUP BY p.id
        ORDER BY totale_ordini DESC
    ")->fetch_all(MYSQLI_ASSOC);
}
