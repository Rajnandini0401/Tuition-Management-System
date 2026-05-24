<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../../config/db.php");

/* ===========================
   CHECK REQUEST
=========================== */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Invalid Request");
}

$roll_no       = $_POST['roll_no'];
$amount_paid   = floatval($_POST['amount_paid']);
$payment_mode  = $_POST['payment_mode'];
$transaction_id = $_POST['transaction_id'];
$payment_date  = date("Y-m-d");

/* ===========================
   FETCH STUDENT
=========================== */
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
$stmt->bind_param("s", $roll_no);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student Not Found");
}

if ($amount_paid <= 0) {
    die("Invalid Amount");
}

/* ===========================
   INSERT PAYMENT
=========================== */
$feeStmt = $conn->prepare("
    INSERT INTO fees (student_id, amount_paid, payment_mode, transaction_id, payment_date)
    VALUES (?, ?, ?, ?, ?)
");

$feeStmt->bind_param(
    "idsss",
    $student['id'],
    $amount_paid,
    $payment_mode,
    $transaction_id,
    $payment_date
);

if (!$feeStmt->execute()) {
    die("Fee Insert Error: " . $feeStmt->error);
}

$feeStmt->close();

/* ===========================
   UPDATE BALANCE
=========================== */
$new_balance = $student['balance_fees'] - $amount_paid;
if ($new_balance < 0) $new_balance = 0;

$updateStmt = $conn->prepare("
    UPDATE students SET balance_fees = ? WHERE id = ?
");

$updateStmt->bind_param("di", $new_balance, $student['id']);
$updateStmt->execute();
$updateStmt->close();

/* ===========================
   REDIRECT TO RECEIPT
=========================== */
header("Location: receipt.php?roll_no=" . $roll_no);
exit();

$conn->close();
?>