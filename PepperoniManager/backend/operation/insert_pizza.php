<?php
require_once "../bootstrap.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: ../../dashboard.php");
    exit;
}

$dbh = $GLOBALS['db'];

$error = null;
$success = null;

$ingredienti = $dbh->executeQuery("
    SELECT *
    FROM INGREDIENTE
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = $_POST['nome'];
    $descrizione = $_POST['descrizione'];

    $prezzoBase = (float) $_POST['prezzo'];

    $disponibilita = isset($_POST['disponibilita']) ? 1 : 0;

    $ingredientiSelezionati = $_POST['ingredienti'] ?? [];

    try {

        $prezzoIngredienti = 0;

        if (!empty($ingredientiSelezionati)) {

            $placeholders = implode(',', array_fill(0, count($ingredientiSelezionati), '?'));

            $stmt = $dbh->prepareQuery("
                SELECT SUM(prezzo) as totale
                FROM INGREDIENTE
                WHERE id IN ($placeholders)
            ");

            $stmt->bind_param(
                str_repeat("i", count($ingredientiSelezionati)),
                ...$ingredientiSelezionati
            );

            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();

            $prezzoIngredienti = (float) $result['totale'];
        }

        $prezzoFinale = $prezzoBase + $prezzoIngredienti;

        $stmt = $dbh->prepareQuery("
            INSERT INTO PIZZA (
                nome,
                descrizione,
                prezzo,
                disponibilita
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssdi",
            $nome,
            $descrizione,
            $prezzoFinale,
            $disponibilita
        );

        $stmt->execute();

        $idPizza = $dbh->getLastInsertId();

        foreach ($ingredientiSelezionati as $idIngrediente) {
            $stmt = $dbh->prepareQuery("
                INSERT INTO RICETTA (
                    pizza_id,
                    ingrediente_id
                )
                VALUES (?, ?)
            ");

            $stmt->bind_param("ii", $idPizza, $idIngrediente);
            $stmt->execute();
        }

        $success = "Pizza inserita con successo!";

    } catch (Exception $e) {
        $error = "Errore inserimento pizza";
    }
}
?>

<!DOCTYPE html>
    <html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuova Pizza</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">

                <h1>🍕 Nuova Pizza</h1>
                <p class="subtitle">Inserisci una pizza nel menu</p>

                <form method="POST" class="form">
                    <input type="text" name="nome" placeholder="Nome pizza" required>

                    <input type="number"
                        step="0.01"
                        name="prezzo"
                        placeholder="Prezzo base (prima degli ingredienti)"
                        required>

                    <select name="dimensione" id="dimensionePizza" required>
                        <option value="">-- Seleziona la dimensione --</option>
                        <option value="S">Piccola (S)</option>
                        <option value="M">Media (M)</option>
                        <option value="L">Grande (L)</option>
                    </select>

                    <select name="impasto" id="impastoPizza" required>
                        <option value="">-- Seleziona l'impasto --</option>
                        <option value="classico">Classico</option>
                        <option value="integrale">Integrale</option>
                        <option value="senza_glutine">Senza glutine</option>
                    </select>

                    <input type="text" name="descrizione" placeholder="Descrizione">

                    <label>
                        <input type="checkbox" name="disponibilita" checked>
                        Disponibile
                    </label>

                    <h3>Ingredienti</h3>

                    <input type="text"  id="js-search" placeholder="Cerca ingrediente...">

                    <div id="js-list">

                        <?php while($row = $ingredienti->fetch_assoc()): ?>

                            <label class="js-item">
                                <input
                                    type="checkbox"
                                    name="ingredienti[]"
                                    value="<?php echo $row['id']; ?>"
                                    class="js-checkbox"
                                    >
                                <span>
                                    <?php echo $row['nome']; ?>
                                </span>
                            </label>

                        <?php endwhile; ?>

                    </div>

                    <div id="js-selected">
                    </div>

                    <button type="submit" class="action-btn">Inserisci Pizza</button>
                    <a class="action-btn" href="../../dashboard.php">Torna alla dashboard</a>
                </form>

                <?php if ($error): ?>
                    <div class="error">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success">
                        <?php echo $success; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>