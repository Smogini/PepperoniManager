<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/vehicles.php';

requireAuth("AMMINISTRATORE");

$conn = Database::getConnection();

$veicoli = getAllVehiclesWithRevisions($conn);

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione Veicoli</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card" style="width:1000px;">
                <?php if ($success): ?>
                    <p style="color:green;">Operazione completata</p>
                <?php endif; ?>

                <?php if ($error): ?>
                    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>
                
                <h1>🚗 Gestione Veicoli</h1>

                <?php foreach ($veicoli as $v): ?>
                    <div style="
                        border: 1px solid #ccc;
                        border-radius: 10px;
                        padding: 20px;
                        margin-bottom: 25px;
                        text-align: left;
                    ">
                        <h2>Veicolo #<?= $v['id'] ?></h2>

                        <p><strong>Tipo:</strong> <?= htmlspecialchars($v['tipo']) ?></p>

                        <form method="POST" action="../actions/update_vehicle.php" class="form">
                            <input type="hidden" name="veicolo_id" value="<?= $v['id'] ?>">

                            <select name="stato_operativo">
                                <option value="OPERATIVO" <?= $v['stato_operativo']==='OPERATIVO'?'selected':'' ?>>
                                    Operativo
                                </option>
                                <option value="MANUTENZIONE" <?= $v['stato_operativo']==='MANUTENZIONE'?'selected':'' ?>>
                                    Manutenzione
                                </option>
                                <option value="NON_DISPONIBILE" <?= $v['stato_operativo']==='NON_DISPONIBILE'?'selected':'' ?>>
                                    Non disponibile
                                </option>
                            </select>

                            <button type="submit">Aggiorna stato</button>
                        </form>

                        <h3>Revisioni</h3>

                        <?php if (empty($v['revisioni'])): ?>
                            <p>Nessuna revisione</p>
                        <?php else: ?>
                            <table>
                                <?php foreach ($v['revisioni'] as $r): ?>
                                    <tr>
                                        <td><?= $r['data'] ?></td>
                                        <td>€<?= number_format($r['prezzo'],2) ?></td>
                                        <td>
                                            <form method="POST" action="../actions/update_revision.php">
                                                <input type="hidden" name="veicolo_id" value="<?= $v['id'] ?>">
                                                <input type="hidden" name="data" value="<?= $r['data'] ?>">

                                                <textarea name="note"><?= htmlspecialchars($r['note']) ?></textarea>

                                                <button type="submit">Salva</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        <?php endif; ?>

                        <h2>🔧 Aggiungi nuova revisione</h2>

                        <form method="POST" action="/PepperoniManager/actions/insert_revision.php" class="form">
                            <input type="hidden" name="veicolo_id" value="<?= $v['id'] ?>">

                            <input type="date" name="data" required>
                            <input type="number" step="0.01" name="prezzo" placeholder="Prezzo (€)" required>
                            <input type="text" name="note" placeholder="Note revisione">

                            <button type="submit">Aggiungi revisione</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <form class="form">
                    <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
                </form>
            </div>
        </div>
    </body>
</html>