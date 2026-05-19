<?php
require_once '../config/auth.php';

requireAuth("AMMINISTRATORE");

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Dipendente</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>👨‍🍳 Nuovo Dipendente</h1>
                <p class="subtitle">Inserisci i dati del dipendente</p>

                <form method="POST" class="form" action="/PepperoniManager/actions/create_employee.php">
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
                    <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error"><?= $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success">
                        Dipendente registrato!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>