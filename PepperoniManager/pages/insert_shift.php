<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/employees.php';
require_once '../queries/vehicles.php';

requireAuth("AMMINISTRATORE");

$conn = Database::getConnection();

$dipendenti = getAllEmployees($conn);
$veicoli = getFunctionalVehicles($conn);

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Turno</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>📅 Nuovo Turno</h1>
                <p class="subtitle">Inserisci un turno di lavoro</p>

                <form method="POST" class="form" action="../actions/insert_shift.php">
                    <input type="date" name="data" required>
                    <input type="time" name="ora_inizio" required>
                    <input type="time" name="ora_fine" required>

                    <h3>Dipendenti</h3>

                    <input type="text" class="js-search" placeholder="Cerca dipendente...">

                    <div class="js-list">
                        <?php foreach($dipendenti as $dipendente): ?>
                            <div class="js-item">
                                <input type="checkbox"
                                    name="dipendenti[]"
                                    value="<?php echo $dipendente['id']; ?>"
                                    class="js-checkbox">
                                <span>
                                    <?= $dipendente['nome'] . " " . $dipendente['cognome']; ?>
                                    (<?= $dipendente['ruolo']; ?>)
                                </span>
                                <?php if ($dipendente['ruolo'] === 'fattorino'): ?>
                                    <select name="veicolo_<?php echo $dipendente['id']; ?>">
                                        <option value="">-- Nessun veicolo --</option>

                                        <?php
                                        foreach($veicoli as $veicolo):
                                        ?>
                                            <option value="<?= $veicolo['id']; ?>">
                                                <?= $veicolo['id'] . " - " . $veicolo['tipo']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="js-selected"></div>

                    <button type="submit">Crea Turno</button>
                    <a href="/PepperoniManager/PAGES/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error">
                        <?= $error; ?>
                    </div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="success">
                        Turno creato con successo!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>
