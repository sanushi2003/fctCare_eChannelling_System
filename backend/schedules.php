
<?php

require_once "config/db.php";

header("Content-Type: application/json");

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

$doctor_id = intval($_GET["doctor_id"] ?? 0);
$hospital_id = intval($_GET["hospital_id"] ?? 0);
$day_of_week = trim($_GET["day_of_week"] ?? "");


/*
|--------------------------------------------------------------------------
| 1. Appointment Page Request
|--------------------------------------------------------------------------
| 
*/

if ($doctor_id > 0 && $hospital_id > 0 && $day_of_week !== "") {

    $stmt = $conn->prepare(
        "SELECT
            s.id,
            s.doctor_id,
            s.hospital_id,
            s.day_of_week,
            s.start_time,
            s.end_time,
            s.status
         FROM schedules s
         WHERE s.doctor_id = ?
           AND s.hospital_id = ?
           AND s.day_of_week = ?
           AND s.status = 'Active'
         ORDER BY s.start_time ASC"
    );

    $stmt->bind_param(
        "iis",
        $doctor_id,
        $hospital_id,
        $day_of_week
    );

}


/*
|--------------------------------------------------------------------------
| 2. Hospitals Page Request
|--------------------------------------------------------------------------
| 
*/

else {

    $stmt = $conn->prepare(
        "SELECT
            s.id,
            s.doctor_id,
            s.hospital_id,
            s.day_of_week,
            s.start_time,
            s.end_time,
            s.status,
            d.name AS doctor_name,
            h.name AS hospital_name
         FROM schedules s

         INNER JOIN doctors d
            ON s.doctor_id = d.id

         INNER JOIN hospitals h
            ON s.hospital_id = h.id

         WHERE s.status = 'Active'

         ORDER BY
            h.name ASC,
            s.day_of_week ASC,
            s.start_time ASC"
    );

}


/*
|--------------------------------------------------------------------------
| Execute Query
|--------------------------------------------------------------------------
*/

$stmt->execute();

$result = $stmt->get_result();

$schedules = [];


/*
|--------------------------------------------------------------------------
| Prepare JSON Data
|--------------------------------------------------------------------------
*/

while ($row = $result->fetch_assoc()) {

    $schedule = [
        "id" => $row["id"],
        "doctor_id" => $row["doctor_id"],
        "hospital_id" => $row["hospital_id"],
        "day_of_week" => $row["day_of_week"],
        "start_time" => date(
            "H:i",
            strtotime($row["start_time"])
        ),
        "end_time" => date(
            "H:i",
            strtotime($row["end_time"])
        ),
        "status" => $row["status"]
    ];


    /*
    |--------------------------------------------------------------
    | These fields are available only for Hospitals Page request
    |--------------------------------------------------------------
    */

    if (isset($row["doctor_name"])) {

        $schedule["doctor_name"] =
            $row["doctor_name"];
    }


    if (isset($row["hospital_name"])) {

        $schedule["hospital_name"] =
            $row["hospital_name"];
    }


    $schedules[] = $schedule;
}


/*
|--------------------------------------------------------------------------
| Send JSON Response
|--------------------------------------------------------------------------
*/

echo json_encode([
    "success" => true,
    "data" => $schedules
]);


$stmt->close();

$conn->close();

?>

