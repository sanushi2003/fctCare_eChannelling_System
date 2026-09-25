
<?php

require_once "config/db.php";

header("Content-Type: application/json");

// ================= CHECK DOCTOR ID =================

if (!isset($_GET["doctor_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "Doctor ID is required."
    ]);

    exit;
}

$doctor_id = intval($_GET["doctor_id"]);

if ($doctor_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid Doctor ID."
    ]);

    exit;
}

// ================= GET ASSIGNED HOSPITALS =================

$stmt = $conn->prepare(
    "SELECT
        h.id,
        h.name,
        h.district,
        h.telephone,
        h.fee
     FROM doctor_hospitals dh
     INNER JOIN hospitals h
        ON dh.hospital_id = h.id
     WHERE dh.doctor_id = ?
     ORDER BY h.name ASC"
);

$stmt->bind_param("i", $doctor_id);

$stmt->execute();

$result = $stmt->get_result();

$hospitals = [];

while ($row = $result->fetch_assoc()) {

    $hospitals[] = [
        "id" => $row["id"],
        "name" => $row["name"],
        "district" => $row["district"],
        "telephone" => $row["telephone"],
        "fee" => $row["fee"]
    ];
}

// ================= RESPONSE =================

echo json_encode([
    "success" => true,
    "data" => $hospitals
]);

$stmt->close();
$conn->close();

?>