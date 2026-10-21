

<?php

require_once "config/db.php";

header("Content-Type: application/json");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$doctor_id = intval($_GET["doctor_id"] ?? 0);

if ($doctor_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid doctor ID."
    ]);

    exit;
}

$stmt = $conn->prepare(
    "SELECT
        h.id,
        h.name,
        h.district,
        h.fee,
        h.telephone
     FROM hospitals h
     WHERE h.id IN (
            SELECT dh.hospital_id
            FROM doctor_hospitals dh
            WHERE dh.doctor_id = ?
        )
        OR h.id IN (
            SELECT s.hospital_id
            FROM schedules s
            WHERE s.doctor_id = ?
              AND s.status = 'Active'
        )
     ORDER BY h.name ASC"
);

$stmt->bind_param("ii", $doctor_id, $doctor_id);
$stmt->execute();

$result = $stmt->get_result();

$hospitals = [];

while ($row = $result->fetch_assoc()) {
    $hospitals[] = $row;
}

echo json_encode([
    "success" => true,
    "version" => "v3",
    "data"    => $hospitals
]);

$stmt->close();
$conn->close();

?>