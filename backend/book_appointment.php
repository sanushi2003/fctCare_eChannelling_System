<?php

require_once "config/db.php";

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/* =========================
   GET FORM DATA
========================= */

$patient_name =
    trim($_POST["patient_name"] ?? "");

$patient_nic =
    trim($_POST["patient_nic"] ?? "");

$patient_phone =
    trim($_POST["patient_phone"] ?? "");

$patient_email =
    trim($_POST["patient_email"] ?? "");

$doctor_id =
    intval($_POST["doctor_id"] ?? 0);

$hospital_id =
    intval($_POST["hospital_id"] ?? 0);

$schedule_id =
    intval($_POST["schedule_id"] ?? 0);

$appointment_date =
    $_POST["appointment_date"] ?? "";

$reason =
    trim($_POST["reason"] ?? "");


/* =========================
   BASIC VALIDATION
========================= */

if (
    empty($patient_name) ||
    empty($patient_nic) ||
    empty($patient_phone) ||
    empty($patient_email) ||
    $doctor_id <= 0 ||
    $hospital_id <= 0 ||
    $schedule_id <= 0 ||
    empty($appointment_date)
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields."
    ]);

    exit;
}


/* =========================
   GET DOCTOR FEE
========================= */

$stmt = $conn->prepare(
    "SELECT fee
     FROM doctors
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $doctor_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid doctor."
    ]);

    exit;
}

$doctor = $result->fetch_assoc();

$doctor_fee =
    floatval($doctor["fee"]);

$stmt->close();


/* =========================
   GET HOSPITAL FEE
========================= */

$stmt = $conn->prepare(
    "SELECT fee
     FROM hospitals
     WHERE id = ?"
);

$stmt->bind_param(
    "i",
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid hospital."
    ]);

    exit;
}

$hospital = $result->fetch_assoc();

$hospital_fee =
    floatval($hospital["fee"]);

$stmt->close();


/* =========================
   CHECK SCHEDULE
========================= */

$stmt = $conn->prepare(
    "SELECT id
     FROM schedules
     WHERE id = ?
     AND hospital_id = ?"
);

$stmt->bind_param(
    "ii",
    $schedule_id,
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid time slot."
    ]);

    exit;
}

$stmt->close();


/* =========================
   CHECK DOCTOR-HOSPITAL
   RELATIONSHIP
========================= */

$stmt = $conn->prepare(
    "SELECT id
     FROM doctor_hospitals
     WHERE doctor_id = ?
     AND hospital_id = ?"
);

$stmt->bind_param(
    "ii",
    $doctor_id,
    $hospital_id
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "success" => false,
        "message" => "This doctor is not available at this hospital."
    ]);

    exit;
}

$stmt->close();


/* =========================
   CALCULATE TOTAL
========================= */

$total_payment =
    $doctor_fee + $hospital_fee;


/* =========================
   INSERT APPOINTMENT
========================= */

$stmt = $conn->prepare(
    "INSERT INTO appointments
    (
        patient_name,
        patient_nic,
        patient_phone,
        patient_email,
        doctor_id,
        hospital_id,
        schedule_id,
        appointment_date,
        reason,
        doctor_fee,
        hospital_fee,
        total_payment
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssiiissddd",
    $patient_name,
    $patient_nic,
    $patient_phone,
    $patient_email,
    $doctor_id,
    $hospital_id,
    $schedule_id,
    $appointment_date,
    $reason,
    $doctor_fee,
    $hospital_fee,
    $total_payment
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "Appointment booked successfully.",
        "appointment_id" => $stmt->insert_id,
        "total_payment" => $total_payment
    ]);

} else {

    echo json_encode([
        "success" => false,
        "message" => "Unable to book appointment."
    ]);
}


$stmt->close();
$conn->close();

?>