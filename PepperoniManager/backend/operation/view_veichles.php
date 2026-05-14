<?php
require_once "../bootstrap.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: /PepperoniManager/dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];

$success = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['update_vehicle'])) {
            $veicoloId = $_POST['veicolo_id'];
            $stato = $_POST['stato_operativo'];

            $stmt = $dbh->prepareQuery("
                UPDATE VEICOLO
                SET stato_operativo = ?
                WHERE id = ?
            ");
            $stmt->bind_param("si", $stato, $veicoloId);
            $stmt->execute();

            $success = "Stato veicolo aggiornato";
        }

        if (isset($_POST['update_revision'])) {
            $veicoloId = $_POST['veicolo_id'];
            $data = $_POST['data'];
            $note = $_POST['note'];

            $stmt = $dbh->prepareQuery("
                UPDATE REVISIONE
                SET note = ?
                WHERE veicolo_id = ?
                AND data = ?
            ");
            $stmt->bind_param(
                "sis",
                $note,
                $veicoloId,
                $data
            );
            $stmt->execute();

            $success = "Note revisione aggiornate";
        }
    } catch (Exception $e) {
        $error = "Errore aggiornamento dati";
    }
}

$query = "
    SELECT
        v.id,
        v.tipo,
        v.stato_operativo,

        r.data,
        r.prezzo,
        r.note

    FROM VEICOLO v

    LEFT JOIN REVISIONE r
        ON v.id = r.veicolo_id

    ORDER BY
        v.id ASC,
        r.data DESC
";
$result = $dbh->executeQuery($query);

$veicoli = [];

while ($row = $result->fetch_assoc()) {
    $id = $row['id'];

    if (!isset($veicoli[$id])) {
        $veicoli[$id] = [
            'id' => $row['id'],
            'tipo' => $row['tipo'],
            'stato_operativo' => $row['stato_operativo'],
            'revisioni' => []
        ];
    }

    if ($row['data'] !== null) {
        $veicoli[$id]['revisioni'][] = [
            'data' => $row['data'],
            'prezzo' => $row['prezzo'],
            'note' => $row['note']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione Veicoli</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card" style="width: 1000px;">
                <h1>🚗 Gestione Veicoli</h1>

                <?php foreach ($veicoli as $veicolo): ?>
                    <div style="
                        border: 1px solid #ccc;
                        border-radius: 10px;
                        padding: 20px;
                        margin-bottom: 25px;
                        text-align: left;
                    ">

                        <h2>Veicolo #<?= $veicolo['id']; ?></h2>

                        <p>
                            <strong>Tipo:</strong>
                            <?= $veicolo['tipo']; ?>
                        </p>

                        <form method="POST" class="form">

                            <input type="hidden" name="veicolo_id" value="<?= $veicolo['id']; ?>">

                            <select name="stato_operativo" required>
                                <option value="OPERATIVO"
                                    <?= $veicolo['stato_operativo'] === 'OPERATIVO' ? 'selected' : ''; ?>>
                                    Operativo
                                </option>
                                <option value="MANUTENZIONE"
                                    <?= $veicolo['stato_operativo'] === 'MANUTENZIONE' ? 'selected' : ''; ?>>
                                    Manutenzione
                                </option>
                                <option value="NON_DISPONIBILE"
                                    <?= $veicolo['stato_operativo'] === 'NON_DISPONIBILE' ? 'selected' : ''; ?>>
                                    Non disponibile
                                </option>
                            </select>

                            <button type="submit" name="update_vehicle">
                                Aggiorna stato
                            </button>
                        </form>

                        <h3>Revisioni</h3>

                        <?php if (count($veicolo['revisioni']) === 0): ?>
                            <p>Nessuna revisione registrata</p>
                        <?php else: ?>

                            <table>
                                <tr>
                                    <th>Data</th>
                                    <th>Prezzo</th>
                                    <th>Note</th>
                                </tr>

                                <?php foreach ($veicolo['revisioni'] as $revisione): ?>

                                    <tr>
                                        <td><?= $revisione['data']; ?></td>
                                        <td>
                                            €<?= number_format(
                                                $revisione['prezzo'],
                                                2
                                            ); ?>
                                        </td>
                                        <td>
                                            <form method="POST">
                                                <input type="hidden" name="veicolo_id" value="<?= $veicolo['id']; ?>">
                                                <input type="hidden" name="data" value="<?= $revisione['data']; ?>">

                                                <textarea name="note" rows="3" style="width: 250px;"><?= $revisione['note']; ?></textarea>
                                                <button type="submit" name="update_revision">Salva</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <?php if ($success): ?>
                    <div class="success">
                        <?= $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="error">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>

                <form class="form">
                    <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
                </form>
            </div>
        </div>
    </body>
</html>