<?php

require_once "config/db.php";

header("Content-Type: application/json");

$sql = "SELECT
            id,
            hospital_code,
            name,
            district,
            telephone,
            facilities,
            fee
        FROM hospitals
        ORDER BY district, name";

$result = $conn->query($sql);

$hospitals = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {
        $hospitals[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $hospitals
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to load hospitals."
    ]);
}

$conn->close();

?>
