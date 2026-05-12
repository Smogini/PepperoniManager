<?php
require_once "../bootstrap.php";

$dbh = $GLOBALS['db'];

if (!isset($_SESSION['role']) ||
    $_SESSION['role'] !== "UTENTE") {

    header("Location: ../login.php");
    exit;
}

$id_utente = $_SESSION['id'];

$error = null;
$success = null;

$pizze = $dbh->executeQuery("
    SELECT *
    FROM PIZZA
    WHERE disponibilita = 1
    ORDER BY nome
");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tipo = $_POST['tipo_acquisizione'] ?? '';
    $pizzeSelezionate = $_POST['pizze'] ?? [];

    if ($tipo == '') {
        $error = "Seleziona il tipo ordine";
    } elseif (count($pizzeSelezionate) == 0) {
        $error = "Seleziona almeno una pizza";
    } else {
        try {
            $query = "
                INSERT INTO ORDINE (
                    Utente_Id,
                    data,
                    stato,
                    tipo_acquisizione
                )
                VALUES (?, ?, ?, ?)
            ";

            $stmt = $dbh->prepareQuery($query);

            $stato = "IN ATTESA";
            date_default_timezone_set('Europe/Rome');
            $data = date("Y-m-d H:i:s");

            $stmt->bind_param("isss", $id_utente, $data, $stato, $tipo);
            $stmt->execute();

            $idOrdine = $dbh->getLastInsertId();
            $riga = 1;

            foreach ($pizzeSelezionate as $idPizza) {
                $pizzaResult = $dbh->executeQuery("
                        SELECT prezzo
                        FROM PIZZA
                        WHERE Id = $idPizza
                    ");
                $pizza = $pizzaResult->fetch_assoc();
                $prezzo = $pizza['prezzo'];
                $query = "
                    INSERT INTO
                    DETTAGLIO_ORDINE (
                        n_riga,
                        Ordine_Id,
                        Pizza_Id,
                        prezzo_finale,
                        note
                    )
                    VALUES (?, ?, ?, ?, ?)
                ";

                $stmt = $dbh->prepareQuery($query);

                $note = null;

                $stmt->bind_param("iiids", $riga, $idOrdine, $idPizza, $prezzo, $note);
                $stmt->execute();
                $riga++;
            }

            if ($tipo === "DOMICILIO") {
                $query = "
                    INSERT INTO CONSEGNA (
                        Ordine_Id,
                        stato
                    )
                    VALUES (?, ?)
                ";

                $stmt = $dbh->prepareQuery($query);

                $statoConsegna = "IN PREPARAZIONE";

                $stmt->bind_param("is", $idOrdine, $statoConsegna);

                $stmt->execute();
            }
            $success = "Ordine creato con successo!";
        } catch (Exception $e) {
            $error = "Errore creazione ordine";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Nuovo Ordine</title>
        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <div class="register-container">
            <div class="register-card">
                <h1>🍕 Nuovo Ordine</h1>
                <p class="subtitle">
                    Seleziona il tipo ordine
                    e le pizze
                </p>
                <form method="POST" class="form">
                    <select name="tipo_acquisizione" required>
                        <option value="">-- Seleziona --</option>
                        <option value="DOMICILIO">Domicilio</option>
                        <option value="ASPORTO">Asporto</option>
                        <option value="TELEFONO">Telefono</option>
                    </select>

                    <h3>Menu</h3>
                    <input type="text" id="pizzaSearch" placeholder="🔎 Cerca pizza...">

                    <div class="grid-list" id="pizzaList">

                        <?php while($pizza = $pizze->fetch_assoc()): ?>

                            <label class="ingredient-row">
                                <input type="checkbox" name="pizze[]" value="<?php echo $pizza['Id']; ?>">
                                <span>
                                    <?php echo $pizza['nome']; ?>
                                    (€<?php echo $pizza['prezzo']; ?>)
                                </span>
                            </label>

                        <?php endwhile; ?>

                    </div>

                    <button type="submit">Crea Ordine</button>
                    <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>

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

        <script src="/PepperoniManager/assets/js/pizza.js"></script>

    </body>
</html>