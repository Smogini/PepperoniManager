<?php
require_once "../bootstrap.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "DIPENDENTE") {
    header("Location: /PepperoniManager/dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];
$dipendente_id = $_SESSION['id'];

$query = "
    SELECT
        T.data,
        T.ora_inizio,
        T.ora_fine,

        V.tipo AS veicolo,

        AT.veicolo_id

    FROM ASSEGNAZIONE_TURNO AT

    INNER JOIN TURNO T
    ON AT.turno_id = T.id

    LEFT JOIN VEICOLO V
    ON AT.veicolo_id = V.id

    WHERE AT.dipendente_id = ?

    ORDER BY
        T.data ASC,
        T.ora_inizio ASC
";

$stmt = $dbh->prepareQuery($query);
$stmt->bind_param("i", $dipendente_id);
$stmt->execute();

$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>I miei turni</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <h2>📅 Turni di lavoro di</h2>

        <?php if ($result->num_rows === 0): ?>
            <p style="text-align:center;">Nessun turno assegnato</p>
        <?php else: ?>

            <table>
                <tr>
                    <th>Data</th>
                    <th>Inizio</th>
                    <th>Fine</th>
                    <th>Veicolo</th>
                </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['data']; ?></td>
                        <td><?php echo substr($row['ora_inizio'], 0, 5); ?></td>
                        <td><?php echo substr($row['ora_fine'], 0, 5); ?></td>
                        <td>
                            <?php if ($row['veicolo_id'] !== null): ?>
                                🚗 <?php echo $row['veicolo']; ?>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
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