<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/pizzas.php';

requireAuth();

$conn = Database::getConnection();

$ingredienti = getAllIngredients($conn);

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuova Pizza</title>
        
        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>🍕 Nuova Pizza</h1>
                <p class="subtitle">Inserisci una pizza nel menu</p>

                <form method="POST" action="../actions/insert_pizza.php" class="form">
                    <input type="text" name="nome" placeholder="Nome pizza" required>
                    <input type="number" step="0.01" name="prezzo" placeholder="Prezzo base" required>
                    
                    <select name="dimensione" id="dimensionePizza" required>
                        <option value="">-- Seleziona la dimensione --</option>
                        <option value="S">S</option>
                        <option value="M">M</option>
                        <option value="L">L</option>
                    </select>

                    <select name="impasto" id="impastoPizza" required>
                        <option value="">-- Seleziona l'impasto --</option>
                        <option value="classico">Classico</option>
                        <option value="integrale">Integrale</option>
                        <option value="senza_glutine">Senza glutine</option>
                    </select>

                    <input type="text" name="descrizione">

                    <label>
                        <input type="checkbox" name="disponibilita" checked>
                        Disponibile
                    </label>

                    <h3>Ingredienti</h3>

                    <input type="text"  id="js-search" placeholder="Cerca ingrediente...">

                    <div id="js-list">
                        <?php foreach ($ingredienti as $i): ?>
                            <label class="js-item">
                                <input
                                    type="checkbox" 
                                    name="ingredienti[]" 
                                    value="<?= $i['id'] ?>"
                                    class="js-checkbox"
                                >
                                <span>
                                    <?= $i['nome'] . " (€ " . $i['prezzo'] . ")" ?>
                                </span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <div id="js-selected"></div>

                    <button type="submit">Inserisci pizza</button>
                    <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <p style="color:red;"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <?php if ($success): ?>
                    <p style="color:green;">Pizza creata</p>
                <?php endif; ?>
            </div>
        </div>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>