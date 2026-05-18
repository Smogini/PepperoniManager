<?php
$mode = $_POST['mode'] ?? 'UTENTE';
$error = $_GET['error'] ?? null;

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/login.css">
    </head>

    <body data-mode="<?= $mode ?>">
        <div class="login-container">
            <div class="login-card">
                <h1>🍕 Pepperoni Manager</h1>
                <p class="subtitle">Accedi al sistema</p>

                <div class="mode-switch">
                    <button type="button" onclick="setMode('UTENTE')">Utente</button>
                    <button type="button" onclick="setMode('DIPENDENTE')">Dipendente</button>
                    <button type="button" onclick="setMode('AMMINISTRATORE')">Admin</button>
                </div>

                <form method="POST" action="../actions/login.php" class="form">
                    <input type="hidden" name="mode" id="modeInput" value="<?= $mode ?>">

                    <input type="text" name="username" placeholder="Username" required>
                    <input type="password" name="password" placeholder="Password" required>

                    <button type="submit">Accedi</button>
                    <div class="register-link">
                        <p>Non hai un account?</p>
                        <a href="register_user.php">Registrati</a>
                    </div>
                </form>

                <?php if ($error): ?>
                    <div class="error">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script src="/PepperoniManager/assets/js/login.js"></script>
    </body>
</html>