<?php
function getEmployeeShifts(mysqli $conn, int $employeeId): array {
    $query = "
        SELECT
            T.data,
            T.ora_inizio,
            T.ora_fine,
            V.tipo AS veicolo,
            AT.veicolo_id
        FROM assegnazione_turno AT
        INNER JOIN turno T ON AT.turno_id = T.id
        LEFT JOIN veicolo V ON AT.veicolo_id = V.id
        WHERE AT.dipendente_id = ?
        ORDER BY
            T.data ASC,
            T.ora_inizio ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $employeeId);
    $stmt->execute();

    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function insertShift(
    mysqli $conn,
    string $data,
    string $ora_inizio,
    string $ora_fine,
    array $dipendenti,
    array $veicoliDipendenti
): bool {

    $shiftQuery = "
        INSERT INTO TURNO (
            data,
            ora_inizio,
            ora_fine
        )
        VALUES (?, ?, ?)
    ";

    $stmt = $conn->prepare($shiftQuery);

    $stmt->bind_param(
        "sss",
        $data,
        $ora_inizio,
        $ora_fine
    );

    if (!$stmt->execute()) {
        return false;
    }

    $idTurno = $conn->insert_id;

    $assignmentQuery = "
        INSERT INTO ASSEGNAZIONE_TURNO (
            dipendente_id,
            turno_id,
            veicolo_id
        )
        VALUES (?, ?, ?)
    ";

    $stmtAssignment = $conn->prepare($assignmentQuery);

    foreach ($dipendenti as $dipendente_id) {
        $veicolo_id = null;

        if (
            isset($veicoliDipendenti[$dipendente_id]) &&
            $veicoliDipendenti[$dipendente_id] !== ''
        ) {
            $veicolo_id = $veicoliDipendenti[$dipendente_id];
        }

        $stmtAssignment->bind_param("iii", $dipendente_id, $idTurno, $veicolo_id);

        if (!$stmtAssignment->execute()) {
            return false;
        }
    }

    return true;
}
