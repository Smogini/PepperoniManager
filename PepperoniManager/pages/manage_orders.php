<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';

requireAuth();

$conn = Database::getConnection();

$role = getUserRole();
$employeeRole = getEmployeeRole();
$ordini = getAllOrders($conn);

$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione Ordini</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <h2>🍕 Gestione Ordini</h2>

        <?php if ($error): ?>
            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Tipo</th>
                <th>Stato</th>
                <th>Azioni</th>
            </tr>

            <?php foreach ($ordini as $ordine): ?>
                <tr>
                    <td>#<?= $ordine['id'] ?></td>
                    <td><?= $ordine['data'] ?></td>
                    <td>
                    <?php
                    echo match($ordine['tipo_acquisizione']) {
                        'DOMICILIO' => '🏠 Domicilio',
                        'ASPORTO' => '🛵 Asporto',
                        default => '—'
                    };
                    ?>
                    </td>
                    <td>
                    <?php
                    echo match($ordine['stato']) {
                        'IN_ATTESA' => '🟡 In attesa',
                        'ACCETTATO' => '🕒 In preparazione',
                        'COMPLETATO' => '🟢 Completato',
                        default => '—'
                    };
                    ?>
                    </td>
                    <td>
                    <?php if ($ordine['stato'] === 'IN_ATTESA'): ?>
                        <form method="POST" action="../actions/accept_order.php">
                            <input type="hidden" name="ordine_id" value="<?= $ordine['id'] ?>">
                            <button type="submit">✅ Accetta</button>
                        </form>
                    <?php elseif ($ordine['stato'] === 'ACCETTATO'): ?>
                        <form method="POST" action="../actions/complete_order.php">
                            <input type="hidden" name="ordine_id" value="<?= $ordine['id'] ?>">
                            <button type="submit">✅ Completa</button>
                        </form>
                    <?php else: ?>
                        🟢 Completato
                    <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <?php if ($error): ?>
            <div class="error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <br>

        <form class="form">
            <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
        </form>
    </body>
</html>