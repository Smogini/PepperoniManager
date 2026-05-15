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