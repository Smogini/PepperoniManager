<?php
require_once '../config/element_status.php';

function insertVehicle(mysqli $conn, string $tipo, string $stato): int {
    $query = "
        INSERT INTO veicolo (tipo, stato_operativo)
        VALUES (?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $tipo, $stato);
    $stmt->execute();

    return $conn->insert_id;
}

function insertOverhaul(
    mysqli $conn,
    int $vehicleId,
    string $data,
    float $prezzo,
    string $note
): bool {

    $query = "
        INSERT INTO revisione (
            veicolo_id,
            `data`,
            prezzo,
            note
        )
        VALUES (?, ?, ?, ?)
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("isds", $vehicleId, $data, $prezzo, $note);

    return $stmt->execute();
}

function getAllVehiclesWithRevisions(mysqli $conn): array {
    $query = "
        SELECT
            v.id,
            v.tipo,
            v.stato_operativo,

            r.data,
            r.prezzo,
            r.note

        FROM veicolo v
        LEFT JOIN revisione r
            ON v.id = r.veicolo_id
        ORDER BY v.id ASC, r.data DESC
    ";

    $result = $conn->query($query);

    $veicoli = [];

    while ($row = $result->fetch_assoc()) {
        $id = $row['id'];

        if (!isset($veicoli[$id])) {
            $veicoli[$id] = [
                'id' => $row['id'],
                'tipo' => $row['tipo'],
                'stato_operativo' => $row['stato_operativo'],
                'revisioni' => []
            ];
        }

        if ($row['data'] !== null) {
            $veicoli[$id]['revisioni'][] = [
                'data' => $row['data'],
                'prezzo' => $row['prezzo'],
                'note' => $row['note']
            ];
        }
    }

    return array_values($veicoli);
}

function updateVehicleStatus(mysqli $conn, int $id, string $stato): bool {
    $query = "
        UPDATE veicolo
        SET stato_operativo = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $stato, $id);

    return $stmt->execute();
}

function updateRevisionNote(mysqli $conn, int $veicoloId, string $data, string $note): bool {
    $query = "
        UPDATE revisione
        SET note = ?
        WHERE veicolo_id = ?
        AND `data` = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("sis", $note, $veicoloId, $data);

    return $stmt->execute();
}

function getFunctionalVehicles(mysqli $conn): array {
    $functionalVehicle = Vehicle_Status::OPERATIVO->value;

    $query = "
        SELECT *
        FROM VEICOLO
        WHERE stato_operativo = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $functionalVehicle);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
