<?php
require_once '../bootstrap.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== "UTENTE") {
    header("Location: /PepperoniManager/dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];
$id_utente = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recensione_ordine'])) {
    $ordineId = $_POST['ordine_id'];
    $voto = $_POST['voto'];
    $commento = trim($_POST['commento']);

    $queryCheck = "
        SELECT Id
        FROM RECENSIONE
        WHERE ordine_id = ?
    ";
    $stmtCheck = $dbh->prepareQuery($queryCheck);
    $stmtCheck->bind_param("i", $ordineId);
    $stmtCheck->execute();

    $alreadyExists = $stmtCheck->get_result()->num_rows > 0;

    if (!$alreadyExists) {
        $statoRecensione = Review_Status::PUBBLICATA->value;
        date_default_timezone_set('Europe/Rome');
        $data = date("Y-m-d H:i:s");
        $queryInsert = "
            INSERT INTO RECENSIONE (
                ordine_id,
                utente_id,
                data_pubblicazione,
                voto,
                commento,
                stato
            )
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmtInsert = $dbh->prepareQuery($queryInsert);
        $stmtInsert->bind_param("iisiss", $ordineId, $id_utente, $data, $voto, $commento, $statoRecensione);
        $stmtInsert->execute();
    }
}

$query = "
    SELECT
        O.id,
        O.data,
        O.stato,
        O.tipo_acquisizione,
        COALESCE(SUM(DO.prezzo_finale), 0) AS totale,

        R.voto,
        R.commento,

        C.stato AS stato_consegna

    FROM ORDINE O

    LEFT JOIN DETTAGLIO_ORDINE DO
    ON O.id = DO.ordine_id

    LEFT JOIN RECENSIONE R
    ON O.id = R.ordine_id

    LEFT JOIN CONSEGNA C
    ON O.id = C.ordine_id

    WHERE O.utente_id = ?

    GROUP BY
        O.id,
        O.data,
        O.stato,
        O.tipo_acquisizione,
        R.voto,
        R.commento,
        C.stato

    ORDER BY O.data DESC
";
$stmt = $dbh->prepareQuery($query);
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
                    <th>Data</th>
                    <th>Modalità</th>
                    <th>Stato</th>
                    <th>Pizze ordinate</th>
                    <th>Totale (€)</th>
                    <th>Consegna</th>
                    <th>Recensione</th>
                </tr>

                <?php
                while ($row = $result->fetch_assoc()):
                    $queryRecensione = "
                        SELECT Id
                        FROM RECENSIONE
                        WHERE ordine_id = ?
                    ";
                    $stmtRecensione = $dbh->prepareQuery($queryRecensione);
                    $stmtRecensione->bind_param("i", $row['Id']);
                    $stmtRecensione->execute();

                    $recensioneEsistente = $stmtRecensione->get_result()->num_rows > 0;
                    $puoRecensire = false;

                    if ($row['voto'] === null) {
                        if ($row['tipo_acquisizione'] === 'ASPORTO' && $row['stato'] === 'COMPLETATO') {
                            $puoRecensire = true;
                        }

                        if ($row['tipo_acquisizione'] === 'DOMICILIO'
                            && $row['stato'] === 'COMPLETATO'
                            && $row['stato_consegna'] === 'CONSEGNATA') {

                            $puoRecensire = true;
                        }
                    }
                    $queryDettagli = "
                        SELECT
                            P.nome,
                            DO.prezzo_finale
                        FROM DETTAGLIO_ORDINE DO
                    
                        INNER JOIN PIZZA P
                            ON DO.pizza_id = P.Id
                    
                        WHERE DO.ordine_id = ?
                    ";
                    $stmtDettagli = $dbh->prepareQuery($queryDettagli);
                    $stmtDettagli->bind_param("i", $row['Id']);
                    $stmtDettagli->execute();
                    
                    $dettagli = $stmtDettagli->get_result();
                ?>

                    <tr>
                        <td><?php echo $row['data']; ?></td>
                        <td>
                            <?php
                                switch ($row['tipo_acquisizione']) {
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
                                switch ($row['stato']) {
                                    case 'IN_ATTESA':
                                        echo "🟡 In attesa di accettazione";
                                        break;
                                    case 'ACCETTATO':
                                        echo "🕒 Ordine in preparazione";
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
                            <ul style="margin:0; padding-left:20px;">
                                <?php while ($pizza = $dettagli->fetch_assoc()): ?>
                                    <li>
                                        <?php echo $pizza['nome']; ?>
                                        -
                                        €<?php echo number_format(
                                            $pizza['prezzo_finale'],
                                            2
                                        ); ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </td>
                        <td>
                            €<?php echo number_format(
                                $row['totale'],
                                2
                            ); ?>
                        </td>
                        <td>
                            <?php if ($row['tipo_acquisizione'] === 'DOMICILIO'): ?>
                                <?php
                                    switch ($row['stato_consegna']) {
                                        case 'IN_ATTESA':
                                            echo "🟡 In attesa";
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
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($puoRecensire): ?>
                                <form method="POST">
                                    <input type="hidden" name="ordine_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="recensione_ordine" value="1">

                                    <select name="voto" required>
                                        <option value="">Voto</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>

                                    <textarea name="commento" placeholder="Commento"></textarea>
                                    <button type="submit">Invia</button>
                                </form>

                            <?php elseif ($row['voto'] !== null): ?>
                                ⭐ <?= $row['voto']; ?>/5<br>
                                <?= htmlspecialchars($row['commento']); ?>
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