
<?php

require_once "config/session.php";
require_once "config/db.php";

header("Content-Type: application/json; charset=UTF-8");


/* =========================
   CHECK REQUEST METHOD
========================= */

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    echo json_encode([
        "success" => false,
        "message" => "Invalid request method."
    ]);

    exit;
}


/* =========================
   CHECK PATIENT LOGIN
========================= */

if (
    !isset($_SESSION["user_type"]) ||
    $_SESSION["user_type"] !== "patient" ||
    !isset($_SESSION["patient_id"])
) {

    http_response_code(401);

    echo json_encode([
        "success" => false,
        "message" => "Please login as a patient before booking an appointment."
    ]);

    exit;
}


/*
   Get patient ID from the server-side session.
   Do not accept patient_id from the browser form.
*/

$patient_id = (int) $_SESSION["patient_id"];

if ($patient_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Invalid patient account. Please login again."
    ]);

    exit;
}


/* =========================
   GET FORM DATA
========================= */

$patient_name = trim($_POST["patient_name"] ?? "");

$patient_nic = trim($_POST["patient_nic"] ?? "");

$patient_phone = trim($_POST["patient_phone"] ?? "");

$patient_email = trim($_POST["patient_email"] ?? "");

$doctor_id = (int) ($_POST["doctor_id"] ?? 0);

$hospital_id = (int) ($_POST["hospital_id"] ?? 0);

$schedule_id = (int) ($_POST["schedule_id"] ?? 0);

$appointment_date = trim($_POST["appointment_date"] ?? "");

$reason = trim($_POST["reason"] ?? "");


/* =========================
   BASIC VALIDATION
========================= */

if (
    $patient_name === "" ||
    $patient_nic === "" ||
    $patient_phone === "" ||
    $patient_email === "" ||
    !filter_var($patient_email, FILTER_VALIDATE_EMAIL) ||
    $doctor_id <= 0 ||
    $hospital_id <= 0 ||
    $schedule_id <= 0 ||
    $appointment_date === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill all required fields correctly."
    ]);

    exit;
}


/* =========================
   VERIFY PATIENT ACCOUNT
========================= */

$stmt = $conn->prepare(
    "SELECT id
     FROM patients
     WHERE id = ?"
);

$stmt->bind_param("i", $patient_id);

$stmt->execute();

$patient_result = $stmt->get_result();

if ($patient_result->num_rows === 0) {

    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "Patient account not found. Please login again."
    ]);

    exit;
}

$stmt->close();


/* =========================
   GET DOCTOR FEE
========================= */

$stmt = $conn->prepare(
    "SELECT fee
     FROM doctors
     WHERE id = ?"
);

$stmt->bind_param("i", $doctor_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "Invalid doctor."
    ]);

    exit;
}

$doctor = $result->fetch_assoc();

$doctor_fee = (float) $doctor["fee"];

$stmt->close();


/* =========================
   GET HOSPITAL FEE
========================= */

$stmt = $conn->prepare(
    "SELECT fee
     FROM hospitals
     WHERE id = ?"
);

$stmt->bind_param("i", $hospital_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "Invalid hospital."
    ]);

    exit;
}

$hospital = $result->fetch_assoc();

$hospital_fee = (float) $hospital["fee"];

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

    $stmt->close();

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

    $stmt->close();

    echo json_encode([
        "success" => false,
        "message" => "This doctor is not available at this hospital."
    ]);

    exit;
}

$stmt->close();


/* =========================
   CALCULATE TOTAL PAYMENT
========================= */

$total_payment = $doctor_fee + $hospital_fee;


/* =========================
   INSERT APPOINTMENT
========================= */

$stmt = $conn->prepare(
    "INSERT INTO appointments
    (
        patient_id,
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
        total_payment,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
);

if (!$stmt) {

    echo json_encode([
        "success" => false,
        "message" => "Unable to prepare the appointment booking."
    ]);

    exit;
}

$stmt->bind_param(
    "issssiiissddd",
    $patient_id,
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


/* =========================
   EXECUTE INSERT
========================= */

if ($stmt->execute()) {

    $appointment_id = $stmt->insert_id;

    echo json_encode([
        "success" => true,
        "message" => "Appointment booked successfully.",
        "appointment_id" => $appointment_id,
        "total_payment" => $total_payment
    ]);

} else {

    error_log("Appointment booking error: " . $stmt->error);

    echo json_encode([
        "success" => false,
        "message" => "Unable to book appointment. Please try again."
    ]);
}


$stmt->close();
$conn->close();

?>

