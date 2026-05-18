<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../config/element_status.php';
require_once '../queries/deliveries.php';

requireAuth();

$conn = Database::getConnection();

$role = getUserRole();
$employeeRole = getEmployeeRole();

if ($role !== "DIPENDENTE" || strtoupper($employeeRole) !== "FATTORINO") {
    header("Location: /PepperoniManager/pages/dashboard.php");
    exit;
}

$courierId = $_SESSION['id'];

$deliveryStatus = Delivery_Status::map();

$available = getAvailableDeliveries(
    $conn,
    $deliveryStatus['CREATA'],
    $deliveryStatus['IN_ATTESA']
);

$courierDeliveries = getDeliveriesByCourierId($conn, $courierId);

$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Consegne</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <h2>🟡 Consegne disponibili</h2>

        <?php if ($error): ?>
            <p style="color:red; text-align:center;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if (empty($available)): ?>
            <p style="text-align:center;">Nessuna consegna disponibile</p>
        <?php else: ?>
        <table>
                <tr>
                    <th>Ordine</th>
                    <th>Data</th>
                    <th>Indirizzo</th>
                    <th>Stato</th>
                    <th>Azione</th>
                </tr>

                <?php foreach ($available as $row): ?>
                    <tr>
                        <td>#<?= $row['ordine_id'] ?></td>
                        <td><?= $row['data'] ?></td>
                        <td>
                            <?= $row['via'] ?> <?= $row['n_civico'] ?>,
                            <?= $row['citta'] ?> <?= $row['cap'] ?>
                        </td>
                        <td>🟡 In attesa</td>
                        <td>
                            <form method="POST" action="../actions/accept_delivery.php">
                                <input type="hidden" name="consegna_id" value="<?= $row['consegna_id'] ?>">

                                <button type="submit">✅ Accetta</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <h2>🔵 Le mie consegne</h2>

        <?php if (empty($courierDeliveries)): ?>
            <p style="text-align:center;">Nessuna consegna assegnata</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Ordine</th>
                    <th>Data</th>
                    <th>Indirizzo</th>
                    <th>Stato</th>
                    <th>Azione</th>
                </tr>

                <?php foreach ($courierDeliveries as $row): ?>
                    <tr>
                        <td>#<?= $row['ordine_id'] ?></td>
                        <td><?= $row['data'] ?></td>
                        <td>
                            <?= $row['via'] ?> <?= $row['n_civico'] ?>,
                            <?= $row['cap'] ?> <?= $row['citta'] ?>
                        </td>
                        <td>
                            <?php
                            echo match($row['stato']) {
                                'IN_ATTESA' => '🟡 In attesa',
                                'IN_CONSEGNA' => '🛵 In consegna',
                                'CONSEGNATA' => '🟢 Consegnata',
                                default => '—'
                            };
                            ?>
                        </td>
                        <td>
                            <?php if ($row['stato'] === 'IN_CONSEGNA'): ?>
                                <form method="POST" action="../actions/complete_delivery.php">
                                    <input type="hidden" name="consegna_id" value="<?= $row['consegna_id'] ?>">
                                    <input type="hidden" name="ordine_id" value="<?= $row['ordine_id'] ?>">

                                    <button type="submit">✅ Completa consegna</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <br>

        <form class="form">
            <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
        </form>
    </body>
</html>