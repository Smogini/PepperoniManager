<?php
require_once "../bootstrap.php";

$dbh = $GLOBALS['db'];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: dashboard.php");
    exit;
}

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $dbh->escapeString($_POST['nome']);
    $cognome = $dbh->escapeString($_POST['cognome']);
    $ruolo = $_POST['ruolo'] ?? '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT) ?? '';

    if ($ruolo === '' || $password === '') {
        $error = "Compila tutti i campi";
    } else {
        try {
            $stmt = $dbh->prepareQuery("
                INSERT INTO PERSONA (nome, cognome)
                VALUES (?, ?)
            ");

            $stmt->bind_param("ss", $nome, $cognome);
            $stmt->execute();

            $id = $dbh->getLastInsertId();

            $stmt = $dbh->prepareQuery("
                INSERT INTO DIPENDENTE (id, ruolo, password)
                VALUES (?, ?, ?)
            ");

            $stmt->bind_param("iss", $id, $ruolo, $password);
            $stmt->execute();

            $success = "Dipendente creato con successo!";

        } catch (Exception $e) {
            $error = "Errore creazione dipendente";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Dipendente</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>

    <div class="register-container">

        <div class="register-card">

            <h1>👨‍🍳 Nuovo Dipendente</h1>
            <p class="subtitle">Inserisci i dati del dipendente</p>

            <form method="POST" class="form">

                <input type="text" name="nome" placeholder="Nome" required>
                <input type="text" name="cognome" placeholder="Cognome" required>

                <select name="ruolo" required>
                    <option value="">-- Seleziona ruolo --</option>
                    <option value="FATTORINO">Fattorino</option>
                    <option value="PIZZAIOLO">Pizzaiolo</option>
                    <option value="CASSIERE">Cassiere</option>
                </select>

                <input type="password" name="password" placeholder="Password del profilo" required>

                <button type="submit">Crea Dipendente</button>
                <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
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