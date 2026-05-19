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
        <title>Nuovo Veicolo</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>🚗 Nuovo Veicolo</h1>
                <p class="subtitle">Inserisci un nuovo mezzo nel sistema</p>

                <form method="POST" class="form" action="/PepperoniManager/actions/insert_vehicle.php">
                    <select name="tipo_veicolo" required>
                        <option value="">-- Tipo --</option>
                        <option value="Automobile">Automobile</option>
                        <option value="Motorino">Motorino</option>
                    </select>

                    <select name="stato_veicolo" required>
                        <option value="">-- Stato --</option>
                        <option value="OPERATIVO">Operativo</option>
                        <option value="MANUTENZIONE">Manutenzione</option>
                        <option value="NON_DISPONIBILE">Non disponibile</option>
                    </select>

                    <h4>Revisione iniziale</h4>

                    <input type="number" step="0.01" name="prezzo" placeholder="Prezzo (€)">
                    <input type="date" name="date">
                    <input type="text" name="note" placeholder="Note">

                    <button type="submit">Inserisci veicolo</button>
                    <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error">
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success">
                        Veicolo inserito con successo
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>