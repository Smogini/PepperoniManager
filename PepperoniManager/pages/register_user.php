<?php
$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Registrazione</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card" id ="card">
                <h1>🍕 Registrazione</h1>
                <p class="subtitle">Crea un nuovo account</p>

                <form method="POST" class="form" action="../actions/register_user.php">
                    <input type="text" name="nome" placeholder="Nome" required>
                    <input type="text" name="cognome" placeholder="Cognome" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>

                    <button type="submit">Registrati</button>
                    <a href="/PepperoniManager/pages/login.php">Torna al login</a>
                </form>

                <?php if ($error): ?>
                    <p style="color:red;">
                        <?= htmlspecialchars($error) ?>
                    </p>
                <?php endif; ?>

                <?php if ($success): ?>
                    <p style="color:green;">
                        Registrazione completata!
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </body>
</html>