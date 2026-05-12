<?php
require_once "backend/bootstrap.php";

$dbh = $GLOBALS['db'];

$entity = $_GET['entity'] ?? null;

$queries = [
    "PERSONA" => "SELECT * FROM Persona",
    "DIPENDENTE" => "SELECT * FROM Dipendente",
    "FATTORINO" => "SELECT * FROM Fattorino",
    "CASSIERE" => "SELECT * FROM Cassiere",
    "PIZZAIOLO" => "SELECT * FROM Pizzaiolo",
    "UTENTE" => "SELECT * FROM Utente",
    "ORDINE" => "SELECT * FROM Ordine",
    "CONSEGNA" => "SELECT * FROM Consegna",
    "PIZZA" => "SELECT * FROM Pizza",
    "DETTAGLIO_ORDINE" => "SELECT * FROM Dettaglio_ordine",
];

if (!isset($queries[$entity])) {
    die("Entità non valida, entita: $entity");
}

$result = $dbh->executeQuery($queries[$entity]);

echo "<h2>$entity</h2>";

if ($result->num_rows == 0) {
    echo "Nessun dato trovato";
    exit;
}

echo "<table border='1' cellpadding='5'>";

$fields = $result->fetch_fields();
echo "<tr>";
foreach ($fields as $f) {
    echo "<th>{$f->name}</th>";
}
echo "</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    foreach ($row as $value) {
        echo "<td>$value</td>";
    }
    echo "</tr>";
}

echo "</table>";
