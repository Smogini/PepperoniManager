<?php
require_once "../bootstrap.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] !== "DIPENDENTE" || strtoupper($_SESSION['employ_role']) !== "FATTORINO") {
    header("Location: ../../dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];
$fattorino_id = $_SESSION['id'];
$order_status = Order_Status::map();
$delivery_status = Delivery_Status::map();

if (isset($_POST['accetta'])) {
    $delivery_id = $_POST['consegna_id'];
    $stmt = $dbh->prepareQuery("
        UPDATE CONSEGNA
        SET fattorino_id = ?, stato = ?
        WHERE id = ?
        AND stato = ?
    ");
    $stmt->bind_param("isis", $fattorino_id, $delivery_status['IN_CONSEGNA'], $delivery_id, $delivery_status['IN_ATTESA']);
    $stmt->execute();
}

if (isset($_POST['consegna'])) {
    $delivery_id = $_POST['consegna_id'];
    $stmt = $dbh->prepareQuery("
        UPDATE CONSEGNA
        SET stato = ?
        WHERE id = ?
        AND stato = ?
    ");
    $stmt->bind_param("sis", $delivery_status['CONSEGNATO'], $delivery_id, $delivery_status['IN_CONSEGNA']);
    $stmt->execute();

    $order_id = $_POST['ordine_id'];
    $stmt = $dbh->prepareQuery("
        UPDATE ORDINE
        SET stato = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $order_status['COMPLETATO'], $order_id);
}

$stmt = $dbh->prepareQuery("
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
    FROM CONSEGNA c
    JOIN ORDINE o ON o.id = c.ordine_id
    WHERE c.fattorino_id IS NULL
      AND c.stato = ? OR c.stato = ?
    ORDER BY o.data ASC
");
$stmt->bind_param("ss", $delivery_status['CREATA'], $delivery_status['IN_ATTESA']);
$stmt->execute();
$disponibili = $stmt->get_result();

$assegnate = $dbh->executeQuery("
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
    FROM CONSEGNA c
    JOIN ORDINE o ON o.id = c.ordine_id
    WHERE c.fattorino_id = $fattorino_id
    ORDER BY o.data DESC
");
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard Fattorino</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <h2>🟡 Consegne disponibili</h2>

        <?php if ($disponibili->num_rows === 0): ?>
            <p style="text-align:center;">Nessun ordine trovato</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ordine</th>
                        <th>Data</th>
                        <th>Indirizzo</th>
                        <th>Stato</th>
                        <th>Azione</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $disponibili->fetch_assoc()): ?>
                        <?php if($row['stato'] !== 'CREATA'): ?>
                            <tr>
                                <td>#<?= $row['ordine_id'] ?></td>
                                <td><?= $row['data'] ?></td>
                                <td>
                                    <?= $row['via'] ?> <?= $row['n_civico'] ?>, <?= $row['citta'] ?>, <?= $row['cap'] ?>
                                </td>
                                <td>
                                    <?php if($row['stato'] === 'IN_ATTESA'): ?>
                                        🟡 In attesa di accettazione
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if($row['stato'] === 'IN_ATTESA'): ?>
                                        <form method="POST">
                                            <input type="hidden" name="consegna_id" value="<?= $row['consegna_id'] ?>">
                                            <button type="submit" name="accetta">✅ Accetta</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <h2>🔵 Le mie consegne</h2>

        <?php if ($assegnate->num_rows === 0): ?>
            <p style="text-align:center;">Nessun ordine trovato</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ordine</th>
                        <th>Data</th>
                        <th>Indirizzo</th>
                        <th>Stato</th>
                        <th>Azione</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = $assegnate->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $row['ordine_id'] ?></td>
                            <td><?= $row['data'] ?></td>
                            <td>
                                <?= $row['via'] ?> <?= $row['n_civico'] ?>, <?= $row['cap'] ?> <?= $row['citta'] ?>
                            </td>
                            <td>
                                <?php
                                    switch ($row['stato']) {
                                        case 'IN_ATTESA':
                                            echo "🟡 In attesa di accettazione";
                                            break;
                                        case 'IN_CONSEGNA':
                                            echo "🛵 In consegna";
                                            break;
                                        case 'CONSEGNATA':
                                            echo "🟢 Consegnata";
                                            break;
                                        default:
                                            echo "—";
                                    }
                                ?>
                            </td>
                            <td>
                                <?php if($row['stato'] === 'IN_CONSEGNA'): ?>
                                    <form method="POST">
                                        <input type="hidden" name="ordine_id" value="<?= $row['ordine_id'] ?>">
                                        <input type="hidden" name="consegna_id" value="<?= $row['consegna_id'] ?>">
                                        <button type="submit" name="consegna">✅ Completa consegna</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <form class="form">
            <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
        </form>
    </body>
</html>
