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

    $nome = $dbh->escapeString($_POST['nome']);
    $prezzo = $_POST['prezzo'];
    $descrizione = $dbh->escapeString($_POST['descrizione']);

    if ($nome === '' || $prezzo === '') {
        $error = "Compila tutti i campi obbligatori";
    } else {

        $query = "
            INSERT INTO INGREDIENTE (
                nome,
                prezzo,
                descrizione
            )
            VALUES (?, ?, ?)
        ";

        $stmt = $dbh->prepareQuery($query);

        $stmt->bind_param(
            "sds",
            $nome,
            $prezzo,
            $descrizione
        );

        if ($stmt->execute()) {
            $success = "Ingrediente inserito con successo!";
        } else {
            $error = "Errore inserimento ingrediente";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Ingrediente</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <div class="register-container">

            <div class="register-card">

                <h1>🧄 Nuovo Ingrediente</h1>
                <p class="subtitle">Aggiungi un ingrediente al menu</p>

                <form method="POST" class="form">

                    <input type="text" name="nome" placeholder="Nome ingrediente" required>

                    <input type="number" step="0.01" name="prezzo" placeholder="Prezzo (€)" required>

                    <input type="text" name="descrizione" placeholder="Descrizione (opzionale)">

                    <button type="submit">Inserisci Ingrediente</button>
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