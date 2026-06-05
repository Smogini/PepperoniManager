<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/users.php';
require_once '../queries/orders.php';
require_once '../queries/reviews.php';

requireAuth("AMMINISTRATORE");

$conn = Database::getConnection();

$utenti = getAllUsers($conn);

$success = $_GET['success'] ?? null;
$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Gestione utenti</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
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
                                <form method="POST" action="../actions/user_status.php">
                                    <input
                                        type="hidden"
                                        name="id_persona"
                                        value="<?= $utente['id_persona'] ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="new_state"
                                        value="BLOCCATO"
                                    >
                                    <button
                                        type="submit"
                                        name="toggle_user"
                                        class="btn-danger"
                                    >
                                        Blocca account
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" action="../actions/user_status.php">
                                    <input
                                        type="hidden"
                                        name="id_persona"
                                        value="<?= $utente['id_persona'] ?>"
                                    >
                                    <input
                                        type="hidden"
                                        name="new_state"
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
                            $ordiniUtente = getUserOrders($conn, $utente['id_persona']);
                            ?>

                            <?php if(!empty($ordiniUtente)): ?>
                                <?php foreach($ordiniUtente as $ordine): ?>
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
                            $recensioni = getUserReviews($conn, $utente['id_persona']);
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
                                                <form method="POST" action="../actions/review_status.php">
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
                                                <form method="POST" action="../actions/review_status.php">
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

                        <?php if ($error): ?>
                            <div class="error">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>
    
                        <?php if ($success): ?>
                            <div class="success">
                                <?= $success ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <form class="form">
            <a href="/PepperoniManager/pages/dashboard.php">Torna alla dashboard</a>
        </form>

        <script src="/PepperoniManager/assets/js/selector.js"></script>
    </body>
</html>
