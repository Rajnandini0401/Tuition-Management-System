<?php 
require_once("../../config/db.php");

/* ===== Get Next Serial ===== */
$result = $conn->query("
    SELECT AUTO_INCREMENT 
    FROM information_schema.TABLES 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'students'
");

$row = $result->fetch_assoc();
$next_id = $row['AUTO_INCREMENT'];

$serial_display = str_pad($next_id, 3, "0", STR_PAD_LEFT);
$admission_number = "UC-" . date("Y") . "-" . $serial_display;

include("../includes/header.php"); 
?>

<!DOCTYPE html>
<html>
<head>
<title>Admission Form</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<link rel="stylesheet" href="../assets/css/admission.css">
<style>/* ===== SIGNATURE ===== */
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

/* ===== STAMP FOOTER ===== */
.stamp-footer {
    margin-top:25px;
    text-align:center;
    font-size:12px;
    border-top:2px dashed #1E3A8A;
    padding-top:10px;
    color:#555;
}

/* PRINT CLEAN */
@media print {
    .no-print,
    .print-btn,
    .btn-back,
    .navbar {
        display:none !important;
    }
}</style>
</head>
<body>

<form method="POST" action="save_admission.php">
<div class="container">

<div class="header">
    <div class="logo">
        <img src="../logo2.jpg" alt="Logo" style="height:80px;">
    </div>

    <div class="header-right">
        <div><strong>Date:</strong> <?= date("d/m/Y"); ?></div>
        <div><strong>Serial No:</strong> <?= $admission_number; ?></div>
    </div>

    <h1>UNIQUE CLASSES</h1>
    <div>Proper Application of Knowledge Moves to Progress</div>
</div>

<div class="title">ADMISSION FORM</div>

<input type="hidden" name="admission_no" value="<?= $admission_number; ?>">

<!-- ================= STUDENT INFO ================= -->
<!-- ================= STUDENT INFO ================= -->
<!-- ================= STUDENT INFO ================= -->
<div class="section">
<div class="section-title">Student Information</div>

<div class="inline-field">
    <label>Full Name:</label>
    <input type="text" name="full_name" required>
</div>

<div class="row">
    <div class="col">
        <label>Class</label>
        <select name="class" required>
            <option value="">Select Class</option>
            <option value="5th">5th</option>
            <option value="6th">6th</option>
            <option value="7th">7th</option>
            <option value="8th">8th</option>
            <option value="9th">9th</option>
            <option value="10th">10th</option>
            <option value="11th">11th</option>
            <option value="12th">12th</option>
            <option value="navodya">5th (नवोदय)</option>
        </select>
    </div>

    <div class="col">
        <label>Medium</label>
        <div class="checkbox-group">
            <input type="checkbox" name="medium[]" value="Marathi"> Marathi
            <input type="checkbox" name="medium[]" value="English"> English
            <input type="checkbox" name="medium[]" value="Semi-English"> Semi-English
        </div>
    </div>
</div>

<!-- SUBJECTS ADDED HERE -->
<div class="inline-field">
    <label style="min-width:120px;">Subjects:</label>

    <div class="checkbox-group">
        <label><input type="checkbox" name="subjects[]" value="Math"> Math</label>
        <label><input type="checkbox" name="subjects[]" value="Science"> Science</label>
        <label><input type="checkbox" name="subjects[]" value="English"> English</label>
        <label><input type="checkbox" name="subjects[]" value="PCM"> PCM</label>
        <label><input type="checkbox" name="subjects[]" value="PCB"> PCB</label>
        <label><input type="checkbox" name="subjects[]" value="PCMB"> PCMB</label>
    </div>
</div>

<div class="row">
    <div class="col">
        <label>School Name</label>
        <select name="school" required>
            <option value="">Select School</option>
            <option>Jay Bharat Vidyalaya</option>
            <option>Lalbahadur Shastri Vidyalaya</option>
            <option>Maharashtra Vidyalaya</option>
            <option>Shivaji Vidyalaya</option>
            <option>Modern English School</option>
            <option>Other</option>
        </select>
    </div>
</div>

<!-- ADDRESS + DOB IN SAME LINE -->
<div class="row">
    <div class="col" style="flex:2;">
        <label>Address</label>
        <textarea name="address" required style="height:30px;"></textarea>
    </div>

    <div class="col">
        <label>Date of Birth</label>
        <input type="date" name="dob">
    </div>
</div>

</div>
<!-- ================= CONTACT ================= -->
<div class="section">
<div class="section-title">Contact Details</div>

<div class="row">
    <div class="col">
        <label>Student Mobile</label>
        <input type="text" name="mobile" required>
    </div>

    <div class="col">
        <label>WhatsApp Number</label>
        <input type="text" name="whatsapp">
    </div>
</div>
</div>

<!-- ================= FEES ================= -->
<div class="section">
<div class="section-title">Fee Details</div>

<div class="fee-row">
    <div class="fee-box">
        <label>Total Fees</label>
        <input type="number" name="total_fees" id="total" required>
    </div>

    <div class="fee-box">
        <label>Amount Paid</label>
        <input type="number" name="amount_paid" id="paid" required>
    </div>

    <div class="fee-box">
        <label>Payment Mode</label>
        <select name="payment_mode" id="payment_mode" required>
            <option value="">Select Mode</option>
            <option value="Cash">Cash</option>
            <option value="Online">Online</option>
        </select>
    </div>

    <div class="fee-box">
        <label>Balance Fees</label>
        <input type="number" name="balance_fees" id="balance" readonly>
    </div>
</div>

<div id="transaction_box" style="display:none;">
    <label>Transaction ID</label>
    <input type="text" name="transaction_id">
</div>
</div>

<!-- ================= SIGNATURE ================= -->
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

<!-- ================= STAMP FOOTER ================= -->
<div class="stamp-footer">
    UNIQUE CLASSES<br>
    Bank Colony Road, Nilanga<br>
    Contact: +91 8308433436
</div>

<div class="no-print" style="text-align:center; margin-top:20px;">
    <button type="submit">Submit Admission</button>
</div>

</div>
</form>

<div class="print-btn" style="text-align:center; margin:20px;">
    <button onclick="window.print()">Print</button>
    <a href="../index.php" class="btn-back">Back to Dashboard</a>
</div>

<script src="../assets/js/admission.js"></script>

</body>
</html>