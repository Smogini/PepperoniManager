<?php
require_once "../bootstrap.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: /PepperoniManager/dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];

$error = null;
$success = null;

$dipendenti = $dbh->executeQuery("
    SELECT
        p.id,
        p.nome,
        p.cognome,
        d.ruolo
    FROM PERSONA p

    INNER JOIN DIPENDENTE d
        ON p.id = d.id

    ORDER BY
        p.cognome ASC,
        p.nome ASC
");

$veicoli = $dbh->executeQuery("
    SELECT *
    FROM VEICOLO
    WHERE stato_operativo = 'OPERATIVO'
    ORDER BY tipo ASC
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = $_POST['data'];
    $ora_inizio = $_POST['ora_inizio'];
    $ora_fine = $_POST['ora_fine'];
    $dipendentiSelezionati = $_POST['dipendenti'] ?? [];

    if ($ora_inizio >= $ora_fine) {
        $error = "Inserisci un orario valido!";
    } else {
        try {
            $stmt = $dbh->prepareQuery("
                INSERT INTO TURNO (
                    data,
                    ora_inizio,
                    ora_fine
                )
                VALUES (?, ?, ?)
            ");
            $stmt->bind_param("sss", $data, $ora_inizio, $ora_fine);
            $stmt->execute();
    
            $turno_id = $dbh->getLastInsertId();
    
            foreach ($dipendentiSelezionati as $dipendente_id) {
                $chiaveVeicolo = "veicolo_" . $dipendente_id;
    
                $veicolo_id = null;
    
                if (isset($_POST[$chiaveVeicolo]) && $_POST[$chiaveVeicolo] !== '') {
                    $veicolo_id = $_POST[$chiaveVeicolo];
                }
    
                $stmt = $dbh->prepareQuery("
                    INSERT INTO ASSEGNAZIONE_TURNO (
                        dipendente_id,
                        turno_id,
                        veicolo_id
                    )
                    VALUES (?, ?, ?)
                ");
    
                $stmt->bind_param(
                    "iii",
                    $dipendente_id,
                    $turno_id,
                    $veicolo_id
                );
                $stmt->execute();
            }
            $success = "Turno creato con successo!";
        } catch (Exception $e) {
            $error = "Errore creazione turno";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Turno</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">

                <h1>📅 Nuovo Turno</h1>
                <p class="subtitle">Inserisci un turno di lavoro</p>

                <form method="POST" class="form">
                    <input type="date" name="data" required>
                    <input type="time" name="ora_inizio" required>
                    <input type="time" name="ora_fine" required>

                    <h3>Dipendenti</h3>

                    <input type="text" class="js-search" placeholder="Cerca dipendente...">

                    <div class="js-list">

                        <?php while($dipendente = $dipendenti->fetch_assoc()): ?>

                            <div class="js-item">

                                <input type="checkbox"
                                    name="dipendenti[]"
                                    value="<?php echo $dipendente['id']; ?>"
                                    class="js-checkbox">

                                <span>
                                    <?= $dipendente['nome'] . " " . $dipendente['cognome']; ?>
                                    (<?= $dipendente['ruolo']; ?>)
                                </span>

                                <?php if ($dipendente['ruolo'] === 'fattorino'): ?>

                                    <select name="veicolo_<?php echo $dipendente['id']; ?>">
                                        <option value="">-- Nessun veicolo --</option>

                                        <?php
                                        $veicoli->data_seek(0);
                                        while($veicolo = $veicoli->fetch_assoc()):
                                        ?>

                                            <option value="<?php echo $veicolo['id']; ?>">
                                                <?= $veicolo['id'] . " - " . $veicolo['tipo']; ?>
                                            </option>

                                        <?php endwhile; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <div class="js-selected"></div>

                    <button type="submit">Crea Turno</button>
                    <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>