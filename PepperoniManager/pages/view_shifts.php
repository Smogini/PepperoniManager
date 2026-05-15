<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/shifts.php';

requireAuth();

$conn = Database::getConnection();

if (getUserRole() !== "DIPENDENTE") {
    header("Location: /PepperoniManager/pages/dashboard.php");
    exit;
}

$employeeId = $_SESSION['id'];

$shifts = getEmployeeShifts($conn, $employeeId);
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>I miei turni</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <h2>📅 I miei turni di lavoro</h2>

        <?php if (empty($shifts)): ?>
            <p style="text-align:center;">Nessun turno assegnato</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>Data</th>
                    <th>Inizio</th>
                    <th>Fine</th>
                    <th>Veicolo</th>
                </tr>

                <?php foreach ($shifts as $shift): ?>
                    <tr>
                        <td><?= htmlspecialchars($shift['data']) ?></td>
                        <td><?= substr($shift['ora_inizio'], 0, 5) ?></td>
                        <td><?= substr($shift['ora_fine'], 0, 5) ?></td>
                        <td>
                            <?php if ($shift['veicolo_id'] !== null): ?>
                                🚗 <?= htmlspecialchars($shift['veicolo']) ?>
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