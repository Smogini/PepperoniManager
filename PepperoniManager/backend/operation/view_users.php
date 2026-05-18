<?php
require_once '../bootstrap.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== "AMMINISTRATORE") {
    header("Location: ../login.php");
    exit;
}

$dbh = $GLOBALS['db'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['toggle_utente'])) {

        $utenteId = intval($_POST['utente_id']);
        $nuovoStato = $_POST['nuovo_stato'];

        $query = "
            UPDATE UTENTE
            SET stato = ?
            WHERE id_persona = ?
        ";

        $stmt = $dbh->prepareQuery($query);

        $stmt->bind_param(
            "si",
            $nuovoStato,
            $utenteId
        );

        $stmt->execute();
    }

    if (isset($_POST['toggle_recensione'])) {

        $recensioneId = intval($_POST['recensione_id']);
        $nuovoStato = $_POST['nuovo_stato'];

        $query = "
            UPDATE RECENSIONE
            SET stato = ?
            WHERE id = ?
        ";

        $stmt = $dbh->prepareQuery($query);

        $stmt->bind_param(
            "si",
            $nuovoStato,
            $recensioneId
        );

        $stmt->execute();
    }

    header("Location: view_users.php");
    exit;
}

$queryUtenti = "
    SELECT
        u.id_persona AS utente_id,
        u.*,

        p.nome,
        p.cognome

    FROM UTENTE u
    INNER JOIN PERSONA p
        ON u.id_persona = p.id

    ORDER BY p.cognome, p.nome
";

$utenti = $dbh->executeQuery($queryUtenti);
?>

<!DOCTYPE html>
<html lang="it">

    <head>

        <meta charset="UTF-8">
        <title>Gestione utenti</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/insert.css">
    </head>

    <body>
        <div class="page-container">
            <h1>Utenti iscritti</h1>

            <input
                type="text"
                class="searchbar js-search"
                placeholder="Cerca utenti..."
            >

            <div class="js-list">
                <?php foreach($utenti as $utente): ?>
                    <div class="utente-card js-item">
                        <div class="utente-header">
                            <div class="utente-title">
                                <h2>
                                    <?= htmlspecialchars($utente['nome']) ?>
                                    <?= htmlspecialchars($utente['cognome']) ?>
                                </h2>

                                <span>
                                    @<?= htmlspecialchars($utente['username']) ?>
                                </span>
                            </div>

                            <div class="badge">
                                <?= htmlspecialchars($utente['stato']) ?>
                            </div>
                        </div>

                        <div class="utente-info">
                            <div class="info-box">
                                <strong>Email</strong>

                                <br>

                                <?= htmlspecialchars($utente['email']) ?>
                            </div>

                            <div class="info-box">
                                <strong>Registrazione</strong>

                                <br>

                                <?= htmlspecialchars($utente['data_registrazione']) ?>
                            </div>
                        </div>

                        <div class="actions">
                            <?php if($utente['stato'] === "ATTIVO"): ?>
                                <form method="POST">
                                    <input
                                        type="hidden"
                                        name="utente_id"
                                        value="<?= $utente['utente_id'] ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="nuovo_stato"
                                        value="BLOCCATO"
                                    >
                                    <button
                                        type="submit"
                                        name="toggle_utente"
                                        class="btn-danger"
                                    >
                                        Blocca account
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST">
                                    <input
                                        type="hidden"
                                        name="utente_id"
                                        value="<?= $utente['utente_id'] ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="nuovo_stato"
                                        value="ATTIVO"
                                    >
                                    <button
                                        type="submit"
                                        name="toggle_utente"
                                        class="btn-success"
                                    >
                                        Riattiva account
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>

                        <details>
                            <summary>Ordini</summary>

                            <?php
                            $queryOrdini = "
                                SELECT *
                                FROM ORDINE
                                WHERE utente_id = ?
                                ORDER BY data DESC
                            ";
                            $stmtOrdini = $dbh->prepareQuery($queryOrdini);
                            $stmtOrdini->bind_param("i", $utente['utente_id']);
                            $stmtOrdini->execute();

                            $ordini = $stmtOrdini
                                ->get_result()
                                ->fetch_all(MYSQLI_ASSOC);
                            ?>

                            <?php if(count($ordini) > 0): ?>
                                <?php foreach($ordini as $ordine): ?>
                                    <div class="ordine-item">
                                        <strong>
                                            Ordine #<?= $ordine['id'] ?>
                                        </strong>

                                        <br>

                                        <?= $ordine['data'] ?>

                                        <br>

                                        <?= $ordine['tipo_acquisizione'] ?>

                                        <br>

                                        <?= $ordine['stato'] ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Nessun ordine effettuato.</p>
                            <?php endif; ?>

                        </details>

                        <details>

                            <summary>
                                Recensioni
                            </summary>

                            <?php
                            $queryRecensioni = "
                                SELECT
                                    r.*

                                FROM RECENSIONE r

                                INNER JOIN ORDINE o
                                    ON r.ordine_id = o.id

                                WHERE o.utente_id = ?

                                ORDER BY r.data_pubblicazione DESC
                            ";
                            $stmtRecensioni = $dbh->prepareQuery($queryRecensioni);
                            $stmtRecensioni->bind_param(
                                "i",
                                $utente['utente_id']
                            );
                            $stmtRecensioni->execute();

                            $recensioni = $stmtRecensioni->get_result()->fetch_all(MYSQLI_ASSOC);
                            ?>

                            <?php if(count($recensioni) > 0): ?>
                                <?php foreach($recensioni as $recensione): ?>
                                    <div class="recensione-item">
                                        <strong>
                                            Ordine #<?= $recensione['ordine_id'] ?>
                                        </strong>

                                        <br>

                                        Voto:
                                        <?= $recensione['voto'] ?>/5

                                        <br>

                                        Stato:
                                        <?= $recensione['stato'] ?>

                                        <br>

                                        <?= $recensione['data_pubblicazione'] ?>

                                        <?php if(!empty($recensione['commento'])): ?>
                                            <p>
                                                <?= htmlspecialchars($recensione['commento']) ?>
                                            </p>
                                        <?php endif; ?>
                                        <div class="actions">
                                            <?php if($recensione['stato'] === "PUBBLICATA"): ?>
                                                <form method="POST">
                                                    <input
                                                        type="hidden"
                                                        name="recensione_id"
                                                        value="<?= $recensione['id'] ?>"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        name="nuovo_stato"
                                                        value="RIMOSSA"
                                                    >
                                                    <button
                                                        type="submit"
                                                        name="toggle_recensione"
                                                        class="btn-warning"
                                                    >
                                                        Nascondi recensione
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="POST">
                                                    <input
                                                        type="hidden"
                                                        name="recensione_id"
                                                        value="<?= $recensione['id'] ?>"
                                                    >
                                                    <input
                                                        type="hidden"
                                                        name="nuovo_stato"
                                                        value="PUBBLICATA"
                                                    >
                                                    <button
                                                        type="submit"
                                                        name="toggle_recensione"
                                                        class="btn-success"
                                                    >
                                                        Ripubblica recensione
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p>Nessuna recensione scritta.</p>
                            <?php endif; ?>
                        </details>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <form class="form">
            <a href="/PepperoniManager/dashboard.php">Torna alla dashboard</a>
        </form>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>