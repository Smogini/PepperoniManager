<?php
require_once '../config/auth.php';

startSession();

$role = getUserRole();
$employ_role = getEmployeeRole();
$username = getUsername();

requireAuth($role);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Dashboard</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/dashboard.css">
    </head>

    <body class="<?= strtolower($role) ?>">
        <div class="box">
            <h1>Benvenuto/a <?= htmlspecialchars($username) ?></h1>

            <?php if ($role === "UTENTE"): ?>
                <h2>Area Utente</h2>

                <a href="/PepperoniManager/pages/create_order.php">🍕 Inserisci ordine</a>
                <a href="/PepperoniManager/pages/view_orders.php">📋 Gestisci ordini</a>

            <?php elseif ($role === "DIPENDENTE"): ?>
                <h2>Area Dipendente (<?= htmlspecialchars($employ_role) ?>)</h2>

                <?php if ($employ_role === 'FATTORINO'): ?>
                    <a href="/PepperoniManager/pages/manage_deliveries.php">🛵 Gestisci consegne</a>
                <?php else: ?>
                    <a href="/PepperoniManager/pages/manage_orders.php">📦 Gestisci ordini</a>
                <?php endif; ?>

                <a href="/PepperoniManager/pages/view_shifts.php">🕒 Visualizza turni</a>

                <?php elseif ($role === "AMMINISTRATORE"): ?>
                    <h2>🛠️ Area Amministratore</h2>

                    <a href="/PepperoniManager/pages/register_employee.php">👨‍🍳 Inserisci dipendente</a>
                    <a href="/PepperoniManager/pages/insert_vehicle.php">🚗 Inserisci veicolo</a>
                    <a href="/PepperoniManager/pages/manage_vehicles.php">🔧 Gestisci veicoli</a>
                    <a href="/PepperoniManager/pages/insert_pizza.php">🍕 Inserisci pizza</a>
                    <a href="/PepperoniManager/pages/insert_ingredient.php">🧀 Inserisci ingrediente</a>
                    <a href="/PepperoniManager/pages/insert_shift.php">📅 Inserisci turno</a>
                    <a href="/PepperoniManager/pages/manage_users.php">👥 Gestisci utenti</a>
                    <a href="/PepperoniManager/pages/manage_reports.php">📊 Statistiche e report</a>
            <?php endif; ?>

            <br><br>

            <a href="/PepperoniManager/actions/logout.php">Logout</a>
        </div>
    </body>
</html>
