<?php 
require_once("../../config/db.php");

if (!isset($_GET['roll_no'])) {
    die("Roll Number Missing");
}

$roll_no = $_GET['roll_no'];

/* ===== FETCH STUDENT ===== */
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
$stmt->bind_param("s", $roll_no);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student Not Found");
}

/* ===== TOTAL PAID ===== */
$total_paid = 0;

$paidStmt = $conn->prepare("
    SELECT SUM(amount_paid) as total_paid 
    FROM fees 
    WHERE student_id = ?
");
$paidStmt->bind_param("i", $student['id']);
$paidStmt->execute();
$paidResult = $paidStmt->get_result();
$paidRow = $paidResult->fetch_assoc();

if ($paidRow && $paidRow['total_paid']) {
    $total_paid = $paidRow['total_paid'];
}

/* ===== LAST PAYMENT ===== */
$lastStmt = $conn->prepare("
    SELECT payment_mode, transaction_id 
    FROM fees 
    WHERE student_id = ?
    ORDER BY id DESC
    LIMIT 1
");
$lastStmt->bind_param("i", $student['id']);
$lastStmt->execute();
$lastResult = $lastStmt->get_result();
$lastPayment = $lastResult->fetch_assoc();

include("../includes/header.php");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admission Copy</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/admission.css">

<style>
input, select, textarea {
    pointer-events:none;
    background:#f3f3f3 !important;
}

.signature-row {
    display:flex;
    justify-content:space-between;
    margin-top:25px;
    gap:20px;
}

.sign-box {
    width:30%;
    text-align:center;
    font-size:13px;
}

.stamp-footer {
    margin-top:25px;
    text-align:center;
    font-size:12px;
    border-top:2px dashed #1E3A8A;
    padding-top:10px;
    color:#555;
}

@media print {
    .navbar,
    .print-btn {
        display:none !important;
    }
}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <div class="logo">
        <img src="../logo2.jpg" style="height:80px;">
    </div>

    <div class="header-right">
        <div><strong>Date:</strong> <?= date("d/m/Y", strtotime($student['admission_date'])); ?></div>
        <div><strong>Roll No:</strong> <?= $student['roll_no']; ?></div>
    </div>

    <h1>UNIQUE CLASSES</h1>
    <div>Proper Application of Knowledge Moves to Progress</div>
</div>

<div class="title">ADMISSION FORM COPY</div>

<!-- STUDENT INFO -->
<div class="section">
<div class="section-title">Student Information</div>

<div class="inline-field">
    <label>Full Name:</label>
    <input type="text" value="<?= htmlspecialchars($student['full_name']) ?>">
</div>

<div class="row">
    <div class="col">
        <label>Class</label>
        <input type="text" value="<?= htmlspecialchars($student['class']) ?>">
    </div>

    <div class="col">
        <label>Date of Birth</label>
        <input type="text" value="<?= htmlspecialchars($student['dob']) ?>">
    </div>
</div>

<label>School Name</label>
<input type="text" value="<?= htmlspecialchars($student['school']) ?>">

<label>Address</label>
<textarea><?= htmlspecialchars($student['address']) ?></textarea>

</div>

<!-- CONTACT -->
<div class="section">
<div class="section-title">Contact Details</div>

<div class="row">
    <div class="col">
        <label>Student Mobile</label>
        <input type="text" value="<?= htmlspecialchars($student['mobile']) ?>">
    </div>

    <div class="col">
        <label>WhatsApp Number</label>
        <input type="text" value="<?= htmlspecialchars($student['whatsapp']) ?>">
    </div>
</div>
</div>

<!-- FEES -->
<div class="section">
<div class="section-title">Fee Details</div>

<div class="fee-row">
    <div class="fee-box">
        <label>Total Fees</label>
        <input type="text" value="₹<?= $student['total_fees'] ?>">
    </div>

    <div class="fee-box">
        <label>Total Paid</label>
        <input type="text" value="₹<?= $total_paid ?>">
    </div>

    <div class="fee-box">
        <label>Balance Fees</label>
        <input type="text" value="₹<?= $student['balance_fees'] ?>">
    </div>
</div>

<div class="row" style="margin-top:15px;">
    <div class="col">
        <label>Last Payment Mode</label>
        <input type="text" value="<?= $lastPayment['payment_mode'] ?? '-' ?>">
    </div>

    <div class="col">
        <label>Transaction ID</label>
        <input type="text" value="<?= $lastPayment['transaction_id'] ?: '-' ?>">
    </div>
</div>

</div>

<!-- SIGNATURE -->
<div class="section">
<div class="section-title">Declaration</div>

<p>I hereby declare that the above information provided is true and correct.</p>

<div class="signature-row">
    <div class="sign-box">
        ___________________________<br>
        Student Signature
    </div>

    <div class="sign-box">
        ___________________________<br>
        Parent / Guardian Signature
    </div>

    <div class="sign-box">
        ___________________________<br>
        Authorized Signatory
    </div>
</div>
</div>

<!-- STAMP -->
<div class="stamp-footer">
    UNIQUE CLASSES<br>
    Bank Colony Road, Nilanga<br>
    Contact: +91 8308433436
</div>

</div>

<div class="print-btn" style="text-align:center; margin:20px;">
    <button onclick="window.print()">Print Copy</button>
</div>
<!-- <?php include("../includes/footer.php"); ?> -->
</body>

</html>

<?php
$stmt->close();
$paidStmt->close();
$lastStmt->close();
$conn->close();
?>