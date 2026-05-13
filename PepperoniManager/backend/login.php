<?php
require_once 'bootstrap.php';

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit;
}

$dbh = $GLOBALS['db'];
$error = null;
$mode = $_POST['mode'] ?? 'UTENTE';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['login'])) {

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $username = $dbh->escapeString($username);

    $roleFound = false;

    if ($mode === "UTENTE") {
        $res = $dbh->executeQuery("SELECT * FROM Utente WHERE username='$username'");

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['role'] = "UTENTE";
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $roleFound = true;
            }
        }
    }

    if ($mode === "DIPENDENTE") {
        $res = $dbh->executeQuery("
            SELECT p.id, p.nome, d.ruolo, d.password
            FROM Persona p
            JOIN Dipendente d ON p.id = d.id
            WHERE p.nome='$username'
        ");

        if ($res && $res->num_rows === 1) {
            $user = $res->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['role'] = "DIPENDENTE";
                $_SESSION['id'] = $user['id'];
                $_SESSION['employ_role'] = $user['ruolo'];
                $_SESSION['username'] = $user['nome'];
                $roleFound = true;
            }
        }
    }

    if ($mode === "AMMINISTRATORE") {
        if ($username == "admin" && $password == "admin") {
            $_SESSION['role'] = "AMMINISTRATORE";
            $_SESSION['id'] = -1;
            $roleFound = true;
        }
    }

    if ($roleFound) {
        header("Location: ../dashboard.php");
        exit;
    } else {
        $error = "Credenziali non valide";
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Login</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/login.css">
    </head>

    <body data-mode="<?php echo $mode; ?>">

    <div class="login-container">

        <div class="login-card" id="card">

            <h1>🍕 Pepperoni Manager</h1>
            <p class="subtitle">Accedi al sistema</p>

            <div class="mode-switch">
                <button type="button" onclick="setMode('UTENTE')">Utente</button>
                <button type="button" onclick="setMode('DIPENDENTE')">Dipendente</button>
                <button type="button" onclick="setMode('AMMINISTRATORE')">Admin</button>
            </div>

            <form method="POST" class="form">

                <input type="hidden" name="mode" id="modeInput" value="<?php echo $mode; ?>">

                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>

                <button type="submit" name="login">Accedi</button>

                <div class="register-link">
                    <p>Non hai un account?</p>
                    <a href="operation/register_user.php">Registrati</a>
                </div>

            </form>

            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

        </div>

    </div>

    <script src="/PepperoniManager/assets/js/login.js"></script>

    </body>
</html>