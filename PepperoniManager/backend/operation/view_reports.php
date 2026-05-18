<?php
require_once '../bootstrap.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: /PepperoniManager/backend/login.php");
    exit;
}

$dbh = $GLOBALS['db'];

$queryClassifica = "
    SELECT
        p.nome,
        COUNT(*) AS totale_ordini

    FROM DETTAGLIO_ORDINE d
    INNER JOIN PIZZA p
        ON d.pizza_id = p.id

    GROUP BY p.id
    ORDER BY totale_ordini DESC
";

$classifica = $dbh->executeQuery($queryClassifica);

$queryRecensioni = "
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
";

$recensioni = $dbh->executeQuery($queryRecensioni);

$queryFatturato = "
    SELECT
        SUM(d.prezzo_finale) AS fatturato
    FROM DETTAGLIO_ORDINE d
";

$result = $dbh->executeQuery($queryFatturato);
$row = $result->fetch_assoc();
$fatturato = $row['fatturato'];
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Admin</title>

        <style>

            .container{
                width:90%;
                max-width:1200px;
                margin:40px auto;
            }

            h1{
                text-align:center;
                margin-bottom:30px;
            }

            .card{
                background:white;
                border-radius:12px;
                padding:20px;
                box-shadow:0 10px 30px rgba(0,0,0,0.2);
                margin-bottom:25px;
            }

            .card h2{
                margin-top:0;
            }

            table{
                width:100%;
                border-collapse:collapse;
                margin-top:10px;
            }

            th, td{
                border:1px solid #ccc;
                padding:10px;
                text-align:center;
            }

            th{
                background:#eee;
            }

            .highlight{
                font-size:22px;
                font-weight:bold;
                color:#27ae60;
                text-align:center;
            }

        </style>
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
                <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
            </form>
        </div>
    </body>
</html>