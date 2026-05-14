<?php
require_once 'backend/bootstrap.php';

if (!isset($_SESSION['id'])) {
    header("Location: backend/login.php");
    exit;
}

$role = $_SESSION['role'];
$employ_role = "";
if (isset($_SESSION['employ_role'])) {
    $employ_role = $_SESSION['employ_role'];
}
$username = "";
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
}
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
            <h1>Benvenuto/a <?= $username ?></h1>

            <?php if ($role === "UTENTE"): ?>
                <h2>Area Utente</h2>

                <a href="backend/operation/insert_order.php">🍕 Inserisci ordine</a>
                <a href="backend/operation/view_orders.php">📋 Gestisci gli ordini effettuati</a>

            <?php elseif ($role === "DIPENDENTE"): ?>
                <h2>Area Dipendente (<?= $employ_role ?>)</h2>

                <?php if($employ_role === 'fattorino'): ?>
                    <a href="backend/operation/view_delivery.php">Visualizza le consegne</a>
                <?php else: ?>
                    <a href="backend/operation/accept_order.php">Accetta gli ordini</a>
                <?php endif; ?>
                <a href="backend/operation/view_shift.php">Visualizza i turni di lavoro</a>

            <?php elseif ($role === "AMMINISTRATORE"): ?>
                <h2>Area Amministratore</h2>

                <a href="backend/operation/register_employee.php">Inserisci un nuovo dipendente</a>
                <a href="backend/operation/insert_veichle.php">Inserisci un nuovo veicolo</a>
                <a href="backend/operation/view_veichles.php">Modifica stato veicolo</a>
                <a href="backend/operation/insert_pizza.php">Inserisci una pizza</a>
                <a href="backend/operation/insert_ingredient.php">Inserisci un ingrediente</a>
                <a href="backend/operation/insert_shift.php">Inserisci un turno</a>
                <a href="backend/operation/view_users.php">Gestisci gli utenti</a>
                <a href="backend/operation/view_reports.php">Controlla statistiche</a>

            <?php endif; ?>

            <br><br>
            <a href="backend/login.php?logout=1">Logout</a>
        </div>
    </body>
</html>