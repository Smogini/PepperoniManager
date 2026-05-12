<?php
require_once '../bootstrap.php';

$dbh = $GLOBALS['db'];

$error = null;
$success = null;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $dbh->escapeString($_POST['nome']);
    $cognome = $dbh->escapeString($_POST['cognome']);
    $email = $dbh->escapeString($_POST['email']);
    $username = $dbh->escapeString($_POST['username']);
    $password = password_hash($dbh->escapeString($_POST['password']), PASSWORD_DEFAULT);

    $check = $dbh->executeQuery("
        SELECT * FROM Utente
        WHERE username = '$username'
    ");

    if ($check->num_rows > 0) {
        $error = "Username già esistente";
    } else {
        
       $query = "
            INSERT INTO PERSONA (nome, cognome)
            VALUES (?, ?)
        ";

        $stmt = $dbh->prepareQuery($query);
        $stmt->bind_param("ss", $nome, $cognome);

        $stmt->execute();

        $idPersona = $dbh->getLastInsertId();


        $query = "
            INSERT INTO UTENTE (
                id,
                email,
                username,
                password,
                stato
            )
            VALUES (?, ?, ?, ?, ?)
        ";

        $stato = 1;

        $stmt = $dbh->prepareQuery($query);
        $stmt->bind_param("issss",
            $idPersona,
            $email,
            $username,
            $password,
            $stato
        );

        if (!$stmt->execute()) {
            throw new Exception("Errore inserimento UTENTE");
        }

        $success = "Registrazione completata!";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Registrazione</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>

    <div class="register-container">

        <div class="register-card" id="card">

            <h1>🍕 Registrazione</h1>
            <p class="subtitle">Crea un nuovo account</p>

            <form method="POST" class="form">

                <input type="text" name="nome" placeholder="Nome" required>
                <input type="text" name="cognome" placeholder="Cognome" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="register">Registrati</button>

            </form>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success">
                    <?php echo $success; ?>
                    <br><br>
                    <a href="../login.php">Vai al login</a>
                </div>
            <?php endif; ?>

        </div>

    </div>

    </body>
</html>