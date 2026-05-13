<?php
require_once "../bootstrap.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "DIPENDENTE" || strtoupper($_SESSION['employ_role']) === "FATTORINO") {
    header("Location: ../../dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];
$order_status = Order_Status::map();
$delivery_status = Delivery_Status::map();

if (isset($_POST['accetta'])) {
    $ordine_id = $_POST['ordine_id'];
    $stmt = $dbh->prepareQuery("
        UPDATE ORDINE
        SET stato = ?
        WHERE id = ?
        AND stato = ?
    ");
    $stmt->bind_param("sis", $order_status['ACCETTATO'], $ordine_id, $order_status['IN_ATTESA']);
    $stmt->execute();
}

if (isset($_POST['completa'])) {
    $ordine_id = $_POST['ordine_id'];
    $stmt = $dbh->prepareQuery("
        UPDATE ORDINE
        SET stato = ?
        WHERE id = ?
        AND stato = ?
    ");
    $stmt->bind_param("sis", $order_status['COMPLETATO'], $ordine_id, $order_status['ACCETTATO']);
    $stmt->execute();

    $stmt = $dbh->prepareQuery("
        UPDATE CONSEGNA
        SET stato = ?
        WHERE ordine_id = ?
    ");
    $stmt->bind_param("si", $delivery_status['IN_ATTESA'], $ordine_id);
    $stmt->execute();
}

$ordini = $dbh->executeQuery("
    SELECT *
    FROM ORDINE
    ORDER BY data DESC
");
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione Ordini</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <h2>🍕 Gestione Ordini</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Tipo</th>
                    <th>Stato</th>
                    <th>Azioni</th>
                </tr>
            </thead>

            <tbody>

            <?php while ($ordine = $ordini->fetch_assoc()): ?>

                <tr>
                    <td>#<?= $ordine['id'] ?></td>
                    <td><?= $ordine['data'] ?></td>
                    <td>
                        <?php
                            switch ($ordine['tipo_acquisizione']) {
                                case 'DOMICILIO':
                                    echo "🏠 Domicilio";
                                    break;
                                case 'ASPORTO':
                                    echo "🛵 Ordine in consegna";
                                    break;
                                default:
                                    echo "—";
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            switch ($ordine['stato']) {
                                case 'IN_ATTESA':
                                    echo "🟡 In attesa di accettazione";
                                    break;
                                case 'ACCETTATO':
                                    echo "🕒 In preparazione";
                                    break;
                                case 'COMPLETATO':
                                    echo "🟢 Completato";
                                    break;
                                default:
                                    echo "—";
                            }
                        ?>
                    </td>
                    <td>
                        <?php if ($ordine['stato'] === "IN_ATTESA"): ?>
                            <form method="POST">
                                <input type="hidden" name="ordine_id" value="<?= $ordine['id'] ?>">
                                <button type="submit" name="accetta">
                                    ✅ Accetta
                                </button>
                            </form>
                        <?php elseif ($ordine['stato'] === "ACCETTATO"): ?>
                            <form method="POST">
                                <input type="hidden" name="ordine_id" value="<?= $ordine['id'] ?>">
                                <button type="submit" name="completa">
                                    🍕 Completa
                                </button>
                            </form>
                        <?php else: ?>
                            🟢 Completato
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endwhile; ?>

            </tbody>

        </table>
        
        <form class="form">
            <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
        </form>

    </body>
</html>