<?php
require_once "../bootstrap.php";

$dbh = $GLOBALS['db'];

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "UTENTE") {
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
                    utente_id,
                    data,
                    stato,
                    tipo_acquisizione
                )
                VALUES (?, ?, ?, ?)
            ";
            $stmt = $dbh->prepareQuery($query);

            $stato = Order_Status::IN_ATTESA->value;
            date_default_timezone_set('Europe/Rome');
            $data = date("Y-m-d H:i:s");

            $stmt->bind_param("isss", $id_utente, $data, $stato, $tipo);
            $stmt->execute();

            $idOrdine = $dbh->getLastInsertId();

            foreach ($pizzeSelezionate as $idPizza) {
                $pizzaResult = $dbh->executeQuery("
                        SELECT prezzo
                        FROM PIZZA
                        WHERE id = $idPizza
                    ");
                $pizza = $pizzaResult->fetch_assoc();
                $prezzo = $pizza['prezzo'];
                $query = "
                    INSERT INTO
                    DETTAGLIO_ORDINE (
                        ordine_id,
                        pizza_id,
                        prezzo_finale,
                        note
                    )
                    VALUES (?, ?, ?, ?)
                ";

                $stmt = $dbh->prepareQuery($query);

                $note = null;

                $stmt->bind_param("iids", $idOrdine, $idPizza, $prezzo, $note);
                $stmt->execute();
            }

            if ($tipo === "DOMICILIO") {
                $via = $_POST['via'] ?? null;
                $n_civico = $_POST['n_civico'] ?? null;
                $citta = $_POST['citta'] ?? null;
                $cap = $_POST['cap'] ?? null;
                $query = "
                    INSERT INTO CONSEGNA (
                        ordine_id,
                        via,
                        n_civico,
                        citta,
                        cap,
                        stato
                    )
                    VALUES (?, ?, ?, ?, ?, ?)
                ";

                $stmt = $dbh->prepareQuery($query);
                $statoConsegna = Delivery_Status::CREATA->value;
                $stmt->bind_param(
                    "isssss",
                    $idOrdine,
                    $via,
                    $n_civico,
                    $citta,
                    $cap,
                    $statoConsegna
                );
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
                    <select name="tipo_acquisizione" id="tipoAcquisizione" required>
                        <option value="">-- Seleziona --</option>
                        <option value="DOMICILIO">Domicilio</option>
                        <option value="ASPORTO">Ritiro in locale</option>
                    </select>

                    <div id="deliveryFields" style="display: none; margin-top: 20px;">
                        <h3>Indirizzo di consegna</h3>

                        <input type="text" name="via" id="via" placeholder="Via">
                        <input type="text" name="n_civico" id="n_civico" placeholder="Numero civico">
                        <input type="text" name="citta" id="citta" placeholder="Città">
                        <input type="text" name="cap" id="cap" placeholder="CAP">
                    </div>

                    <h3>Menu</h3>
                    <input type="text" id="js-search" placeholder="🔎 Cerca pizza...">

                    <div class="js-list" id="pizzaList">

                        <?php while($pizza = $pizze->fetch_assoc()): ?>

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
                        <?php endwhile; ?>
                    </div>

                    <div class="js-selected"></div>

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

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>