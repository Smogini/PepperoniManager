<?php
require_once '../config/database.php';
require_once '../config/auth.php';
require_once '../queries/orders.php';
require_once '../queries/reviews.php';

requireAuth();

$conn = Database::getConnection();

$userId = $_SESSION['id'];

if (getUserRole() !== "UTENTE") {
    header("Location: /PepperoniManager/pages/dashboard.php");
    exit;
}

$result = getUserOrders($conn, $userId);

$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <title>Storico Ordini</title>

        <link rel="stylesheet" href="/PepperoniManager/assets/css/style.css">
    </head>

    <body>
        <h2>📋 Il tuo storico ordini</h2>

        <?php if ($error): ?>
            <p style="color:red; text-align:center;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <?php if (empty($result)): ?>
            <p style="text-align:center;">Nessun ordine trovato</p>
        <?php else: ?>

            <table>
                <tr>
                    <th>Data</th>
                    <th>Modalità</th>
                    <th>Stato</th>
                    <th>Pizze</th>
                    <th>Totale</th>
                    <th>Consegna</th>
                    <th>Recensione</th>
                </tr>

                <?php foreach ($result as $row): ?>
                    <?php
                    $puoRecensire = false;

                    if ($row['voto'] === null) {
                        if (
                            $row['tipo_acquisizione'] === 'ASPORTO'
                            && $row['stato'] === 'COMPLETATO'
                        ) {
                            $puoRecensire = true;
                        }

                        if (
                            $row['tipo_acquisizione'] === 'DOMICILIO'
                            && $row['stato'] === 'COMPLETATO'
                            && $row['stato_consegna'] === 'CONSEGNATA'
                        ) {
                            $puoRecensire = true;
                        }
                    }

                    $dettagliStmt = $conn->prepare("
                        SELECT P.nome, DO.prezzo_finale
                        FROM dettaglio_ordine DO
                        JOIN pizza P ON DO.pizza_id = P.id
                        WHERE DO.ordine_id = ?
                    ");

                    $dettagliStmt->bind_param("i", $row['id']);
                    $dettagliStmt->execute();
                    $dettagli = $dettagliStmt->get_result();
                    ?>

                    <tr>
                        <td><?= $row['data'] ?></td>
                        <td>
                            <?php
                            echo match($row['tipo_acquisizione']) {
                                'DOMICILIO' => '🏠 Domicilio',
                                'ASPORTO' => '🛵 Asporto',
                                default => '—'
                            };
                            ?>
                        </td>
                        <td>
                            <?php
                            echo match($row['stato']) {
                                Order_Status::IN_ATTESA->value => '🟡 In attesa di accettazione',
                                Order_Status::ACCETTATO->value => '🕒 Ordine in preparazione',
                                Order_Status::COMPLETATO->value => '🟢 Completato',
                                default => '—'
                            }
                            ?>
                        </td>
                        <td>
                            <ul>
                                <?php while ($p = $dettagli->fetch_assoc()): ?>
                                    <li>
                                        <?= $p['nome'] ?> - €<?= number_format($p['prezzo_finale'], 2) ?>
                                    </li>
                                <?php endwhile; ?>
                            </ul>
                        </td>
                        <td>€<?= number_format($row['totale'], 2) ?></td>
                        <td>
                            <?php
                            if ($row['tipo_acquisizione'] === 'ASPORTO') {
                                echo match($row['stato']) {
                                    Delivery_Status::CREATA->value => '🕒 In elaborazione',
                                    Delivery_Status::IN_ATTESA->value => '🟡 In attesa di accettazione',
                                    Delivery_Status::IN_CONSEGNA->value => '🛵 In consegna',
                                    Delivery_Status::CONSEGNATA->value => '🟢 Consegnata',
                                    default => '—'
                                };
                            } else {
                                echo '—';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($puoRecensire): ?>

                            <form method="POST" action="../actions/insert_review.php">
                                <input type="hidden" name="ordine_id" value="<?= $row['id'] ?>">

                                <select name="voto" required>
                                    <option value="">Voto</option>
                                    <?php for ($i=1; $i<=5; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?></option>
                                    <?php endfor; ?>
                                </select>
                                <textarea name="commento"></textarea>

                                <button type="submit">Invia</button>
                            </form>
                            <?php elseif ($row['voto'] !== null): ?>
                                ⭐ <?= $row['voto'] ?>/5<br>
                                <?= htmlspecialchars($row['commento']) ?>
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