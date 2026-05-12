<?php
require_once 'backend/bootstrap.php';

if (!isset($_SESSION['role'])) {
    header("Location: backend/login.php");
    exit;
}

$role = $_SESSION['role'];
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/dashboard.css">
    </head>

    <body class="<?php echo strtolower($role); ?>">

    <div class="box">

        <h1>Pepperoni Manager</h1>

        <?php if ($role === "UTENTE"): ?>

            <h2>Area Utente</h2>

            <a href="backend/operation/insert_order.php">🍕 Inserisci ordine</a>

            <a href="backend/operation/view_orders.php">📋 Storico ordini</a>

        <?php elseif ($role === "DIPENDENTE"): ?>

            <h2>Area Dipendente</h2>

        <?php elseif ($role === "AMMINISTRATORE"): ?>

            <h2>Area Amministratore</h2>

            <a href="backend/operation/register_employee.php">Inserisci un nuovo dipendente</a>
            <a href="backend/operation/insert_veichle.php">Inserisci un nuovo veicolo</a>
            <a href="backend/operation/insert_pizza.php">Inserisci una pizza</a>
            <a href="backend/operation/insert_ingredient.php">Inserisci un ingrediente</a>

        <?php endif; ?>

        <br><br>
        <a href="backend/login.php?logout=1">Logout</a>

    </div>

    </body>
</html>