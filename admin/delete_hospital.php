<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

require_once "../backend/config/db.php";


// ================= GET HOSPITAL ID =================

$id = intval($_GET["id"] ?? 0);

if ($id <= 0) {
    header("Location: hospitals.php");
    exit;
}


// ================= DELETE HOSPITAL =================

$stmt = $conn->prepare(
    "DELETE FROM hospitals WHERE id = ?"
);

$stmt->bind_param("i", $id);


if ($stmt->execute()) {

    // Hospital deleted successfully
    header("Location: hospitals.php");
    exit;

} else {

    // Delete failed
    echo "
        <script>
            alert('Failed to delete hospital.');
            window.location.href = 'hospitals.php';
        </script>
    ";

}


$stmt->close();

$conn->close();

?>