<?php
$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Ingrediente</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>🧄 Nuovo Ingrediente</h1>
                <p class="subtitle">Aggiungi un ingrediente al menu</p>

                <form method="POST" class="form" action="../actions/insert_ingredient.php">
                    <input type="text" name="nome" placeholder="Nome ingrediente" required>
                    <input type="number" step="0.01" name="prezzo" placeholder="Prezzo (€)" required>
                    <input type="text" name="descrizione" placeholder="Descrizione (opzionale)">

                    <button type="submit">Inserisci Ingrediente</button>
                    <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success">
                        Ingrediente inserito con successo!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>
