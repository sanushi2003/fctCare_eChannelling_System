<?php

require_once "config/db.php";

header("Content-Type: application/json");

// ================= GET DOCTORS =================

$sql = "SELECT
            id,
            doctor_code,
            name,
            specialty,
            fee
        FROM doctors
        ORDER BY name ASC";

$result = $conn->query($sql);

$doctors = [];

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $doctors[] = [
            "id" => $row["id"],
            "doctor_code" => $row["doctor_code"],
            "name" => $row["name"],
            "specialty" => $row["specialty"],
            "fee" => $row["fee"]
        ];
    }

    echo json_encode([
        "success" => true,
        "data" => $doctors
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to load doctors."
    ]);
}

$conn->close();

?>