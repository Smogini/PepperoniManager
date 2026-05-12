<?php
require_once '../bootstrap.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "UTENTE") {
    header("Location: /PepperoniManager/dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];

$id_utente = $_SESSION['id'];

$stmt = $dbh->prepareQuery("
    SELECT *
    FROM Ordine
    WHERE id = ?
    ORDER BY `data` DESC
");

$stmt->bind_param("i", $id_utente);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Storico Ordini</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>

        <h2>📋 Il tuo storico ordini</h2>

        <?php if ($result->num_rows === 0): ?>

            <p style="text-align:center;">Nessun ordine trovato</p>

        <?php else: ?>

        <table>
            <tr>
                <th>ID Ordine</th>
                <th>Data</th>
                <th>Stato</th>
                <th>Totale (€)</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id_ordine']; ?></td>
                    <td><?php echo $row['data']; ?></td>
                    <td><?php echo $row['stato']; ?></td>
                    <td><?php echo $row['totale']; ?></td>
                </tr>
            <?php endwhile; ?>

        </table>

        <?php endif; ?>

        <br>
        <form class="form">
            <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
        </form>

    </body>
</html>