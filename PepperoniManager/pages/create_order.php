<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/pizzas.php';

requireAuth("UTENTE");

$conn = Database::getConnection();

$pizze = getAvailablePizzas($conn);

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Ordine</title>
        
        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>🍕 Nuovo Ordine</h1>
                <p class="subtitle">
                    Seleziona il tipo ordine
                    e le pizze
                </p>
                <form method="POST" class="form" action="/PepperoniManager/actions/insert_order.php">
                    <select name="tipo_acquisizione" id="tipoAcquisizione" required>
                        <option value="">-- Seleziona --</option>
                        <option value="DOMICILIO">Domicilio</option>
                        <option value="ASPORTO">Ritiro in locale</option>
                    </select>

                    <div name="indirizzo_consegna" id="deliveryFields" style="display: none; margin-top: 20px;">
                        <h3>Indirizzo di consegna</h3>

                        <input type="text" name="via" id="via" placeholder="Via">
                        <input type="text" name="n_civico" id="n_civico" placeholder="Numero civico">
                        <input type="text" name="citta" id="citta" placeholder="Città">
                        <input type="text" name="cap" id="cap" placeholder="CAP">
                    </div>

                    <h3>Menu</h3>
                    <input type="text" id="js-search" placeholder="🔎 Cerca pizza...">

                    <div class="js-list" id="pizzaList">
                        <?php foreach($pizze as $pizza): ?>
                            <label class="js-item">
                                <input
                                    type="checkbox"
                                    name="pizze[]"
                                    value="<?= $pizza['id']; ?>"
                                    class="js-checkbox"
                                >
                                <span>
                                    <?= $pizza['nome']; ?>
                                    (€<?= $pizza['prezzo']; ?>)
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div class="js-selected"></div>

                    <button type="submit">Crea Ordine</button>
                    <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success">
                        Ordine creato con successo!
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>