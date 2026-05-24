<?php
require_once("../../config/db.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: admission_form.php");
    exit();
}

/* ===== GET FORM DATA ===== */
$full_name      = trim($_POST['full_name']);
$class          = trim($_POST['class']);
$school         = $_POST['school'];
$dob            = $_POST['dob'];
$address        = $_POST['address'];
$mobile         = $_POST['mobile'];
$whatsapp       = $_POST['whatsapp'];
$total_fees     = floatval($_POST['total_fees']);
$amount_paid    = floatval($_POST['amount_paid']);
$payment_mode   = $_POST['payment_mode'];
$transaction_id = $_POST['transaction_id'];
$balance_fees   = floatval($_POST['balance_fees']);
$admission_date = date("Y-m-d");

/* ===== SUBJECTS ===== */
$subjects = isset($_POST['subjects']) ? implode(", ", $_POST['subjects']) : "";

/* ===== GENERATE ROLL ===== */
$class_lower = strtolower($class);
$class_number = preg_replace('/[^0-9]/', '', $class_lower);

$prefix = (strpos($class_lower, 'navodya') !== false)
    ? "UN" . $class_number . "N"
    : "UN" . $class_number;

$like_pattern = $prefix . "%";

$rollStmt = $conn->prepare("
    SELECT roll_no 
    FROM students 
    WHERE roll_no LIKE ? 
    ORDER BY id DESC 
    LIMIT 1
");

$rollStmt->bind_param("s", $like_pattern);
$rollStmt->execute();
$rollResult = $rollStmt->get_result();

$nextNumber = 1;
if ($rollResult->num_rows > 0) {
    $lastRoll = $rollResult->fetch_assoc()['roll_no'];
    $nextNumber = intval(substr($lastRoll, -3)) + 1;
}

$roll_no = $prefix . str_pad($nextNumber, 3, "0", STR_PAD_LEFT);
$rollStmt->close();

/* ===== ADMISSION NO ===== */
$admission_no = "ADM" . time();

/* ===== INSERT STUDENT ===== */
$stmt = $conn->prepare("
    INSERT INTO students 
    (roll_no, admission_no, full_name, `class`, subjects, school, dob, address, mobile, whatsapp, total_fees, balance_fees, admission_date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "ssssssssssdds",
    $roll_no,
    $admission_no,
    $full_name,
    $class,
    $subjects,
    $school,
    $dob,
    $address,
    $mobile,
    $whatsapp,
    $total_fees,
    $balance_fees,
    $admission_date
);

if ($stmt->execute()) {

    $student_id = $stmt->insert_id;

    /* ===== FIRST PAYMENT ===== */
    if ($amount_paid > 0) {

        $feeStmt = $conn->prepare("
            INSERT INTO fees
            (student_id, amount_paid, payment_mode, transaction_id, payment_date)
            VALUES (?, ?, ?, ?, ?)
        ");

        $feeStmt->bind_param(
            "idsss",
            $student_id,
            $amount_paid,
            $payment_mode,
            $transaction_id,
            $admission_date
        );

        $feeStmt->execute();
        $feeStmt->close();
    }

    /* 🔥 REDIRECT TO RECEIPT */
    header("Location: ../fees/receipt.php?roll_no=" . $roll_no);
    exit();

} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>