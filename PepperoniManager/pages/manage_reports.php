<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/reports.php';

requireAuth("AMMINISTRATORE");

$conn = Database::getConnection();

$fatturato = getTurnover($conn);
$classifica = getBestPizzas($conn);
$recensioni = getBestReviews($conn);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Admin</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="container">
            <h1>Dashboard Amministratore</h1>

            <div class="card">
                <h2>Fatturato complessivo</h2>

                <div class="highlight">
                    € <?= number_format($fatturato ?? 0, 2) ?>
                </div>
            </div>

            <div class="card">
                <h2>Classifica pizze più ordinate</h2>

                <table>
                    <tr>
                        <th>Pizza</th>
                        <th>Ordini</th>
                    </tr>

                    <?php foreach($classifica as $pizza): ?>
                        <tr>
                            <td><?= htmlspecialchars($pizza['nome']) ?></td>
                            <td><?= $pizza['totale_ordini'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <div class="card">
                <h2>Recensioni utenti</h2>

                <table>
                    <tr>
                        <th>Utente</th>
                        <th>Ordine</th>
                        <th>Voto</th>
                        <th>Commento</th>
                        <th>Data</th>
                        <th>Stato</th>
                    </tr>

                    <?php foreach($recensioni as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['username']) ?></td>
                            <td>#<?= $r['ordine_id'] ?></td>
                            <td><?= $r['voto'] ?>/5</td>
                            <td><?= htmlspecialchars($r['commento']) ?></td>
                            <td><?= $r['data_pubblicazione'] ?></td>
                            <td><?= $r['stato'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>

            <form class="form">
                <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
            </form>
        </div>
    </body>
</html>
