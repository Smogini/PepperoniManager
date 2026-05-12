<?php
require_once "../bootstrap.php";

$dbh = $GLOBALS['db'];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: ../../dashboard.php");
    exit;
}

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $tipo = $dbh->escapeString($_POST['tipo_veicolo']);
    $stato = $dbh->escapeString($_POST['stato_veicolo']);

    if ($tipo == '' || $stato == '') {
        $error = "Seleziona un'opzione corretta";
        exit;
    }

    try {
        $stmt = $dbh->prepareQuery("
            INSERT INTO VEICOLO (tipo, stato_operativo)
            VALUES (?, ?)
        ");

        $stmt->bind_param("ss", $tipo, $stato);
        $stmt->execute();

        $idVeicolo = $dbh->getLastInsertId();

        if (!empty($_POST['prezzo'])) {

            $data = $dbh->escapeString($_POST['date']);
            $prezzo = $dbh->escapeString($_POST['prezzo']);
            $note = $dbh->escapeString($_POST['note']) ?? '';

            $stmt = $dbh->prepareQuery("
                INSERT INTO REVISIONE (
                    Veicolo_Id,
                    data,
                    prezzo,
                    note
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "isds",
                $idVeicolo,
                $data,
                $prezzo,
                $note
            );

            $stmt->execute();
        }

        $success = "Veicolo inserito con successo!";
    } catch (Exception $e) {
        $error = "Errore inserimento veicolo";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Nuovo Veicolo</title>

    <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
</head>

<body>

<div class="register-container">

    <div class="register-card">

        <h1>🚗 Nuovo Veicolo</h1>
        <p class="subtitle">Inserisci un nuovo mezzo nel sistema</p>

        <form method="POST" class="form">

            <select name="tipo_veicolo" required>
                <option value="">-- Seleziona --</option>
                <option value="Automobile">Automobile</option>
                <option value="Motorino">Motorino</option>
            </select>

            <select name="stato_veicolo" required>
                <option value="">-- Seleziona --</option>
                <option value="Operativo">Operativo</option>
                <option value="Guasto">Guasto</option>
            </select>

            <h4>Revisione iniziale</h4>

            <input type="number" step="0.01" min="0.00" name="prezzo" placeholder="Prezzo revisione (€)" required>
            <input type="date" name="date" placeholder="Data di effettuazione" required>
            <input type="text" name="note" placeholder="Note revisione">
            
            <button type="submit">Inserisci Veicolo</button>
            <a href="../../dashboard.php">Torna alla dashboard</a>

        </form>

        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success"><?php echo $success; ?></div>
        <?php endif; ?>

    </div>

</div>

</body>
</html>