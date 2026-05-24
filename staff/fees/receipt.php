<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once("../../config/db.php");

if (!isset($_GET['roll_no']) || empty($_GET['roll_no'])) {
    die("Roll Number Missing");
}

$roll_no = $_GET['roll_no'];

$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
$stmt->bind_param("s", $roll_no);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student Not Found");
}

$feeStmt = $conn->prepare("
    SELECT amount_paid, payment_mode, transaction_id, payment_date 
    FROM fees 
    WHERE student_id = ?
    ORDER BY payment_date ASC
");
$feeStmt->bind_param("i", $student['id']);
$feeStmt->execute();
$fees = $feeStmt->get_result();

$total_paid = 0;
$history = [];

while ($row = $fees->fetch_assoc()) {
    $total_paid += $row['amount_paid'];
    $history[] = $row;
}

$receipt_no = "REC-" . date("Ymd") . "-" . substr($roll_no, -3);
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<style>
    * {
    box-sizing: border-box;
}

body {
    font-family: Arial;
    background:#f2f2f2;
}

.container {
    width: 100%;
    max-width: 180mm;   /* 🔥 safe for A4 */
    margin: 15px auto;
    background:#fff;
    padding:15px;
    border:2px solid #1E3A8A;
    box-sizing:border-box;
}

/* Header */
.receipt-header {
    display:flex;
    justify-content:space-between;
    align-items:center;
    border-bottom:2px solid #1E3A8A;
    padding-bottom:10px;
    margin-bottom:15px;
}

.logo-section {
    display:flex;
    align-items:center;
    gap:12px;
}

.logo-section img {
    width:70px;
}

.institute-info h2 {
    margin:0;
    color:#1E3A8A;
}

.institute-info p {
    margin:2px 0;
    font-size:12px;
}

.meta {
    text-align:right;
    font-size:13px;
}

/* Info Grid */
.info-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:8px;
    margin-bottom:15px;
}

.info-box {
    border:1px solid #ddd;
    padding:6px;
    background:#fafafa;
    font-size:13px;
}

/* Table */
table {
    width:100%;
    border-collapse:collapse;
    font-size:13px;
}

table, th, td {
    border:1px solid #ccc;
}

th {
    background:#1E3A8A;
    color:white;
}

th, td {
    padding:6px;
    text-align:center;
}

/* Summary */
.summary {
    margin-top:12px;
    font-weight:bold;
}

.balance {
    color:red;
}

/* Signature */
.signature {
    margin-top:30px;
    text-align:right;
    font-size:13px;
}

/* Buttons */
.actions {
    text-align:center;
    margin-top:15px;
}

.btn {
    padding:6px 12px;
    background:#1E3A8A;
    color:white;
    border:none;
    border-radius:4px;
    margin:4px;
    cursor:pointer;
}
@page {
    size: A4;
    margin: 5mm;
}

@media print {

    body {
        background:white;
        margin:0;
    }

    .container {
        width: 100%;
        max-width: 100%;
        padding:10px;
        border:2px solid #000;
        margin:0;
    }

    .actions {
        display:none;
    }
}
</style>

<div class="container">

<div class="receipt-header">
    <div class="logo-section">
        <img src="../logo2.jpg" alt="Logo">
        <div class="institute-info">
            <h2>UNIQUE CLASSES</h2>
            <p>Proper Application of Knowledge Moves to Progress</p>
            <p>Bank Colony Road, Nilanga</p>
            <p>Contact: +91 8308433435/8308433436</p>
        </div>
    </div>

    <div class="meta">
        <div><strong>Receipt No:</strong> <?= $receipt_no ?></div>
        <div><strong>Date:</strong> <?= date("d/m/Y") ?></div>
    </div>
</div>

<div class="info-grid">
    <div class="info-box"><strong>Roll No:</strong> <?= htmlspecialchars($student['roll_no']) ?></div>
    <div class="info-box"><strong>Name:</strong> <?= htmlspecialchars($student['full_name']) ?></div>
    <div class="info-box"><strong>Class:</strong> <?= htmlspecialchars($student['class']) ?></div>
    <div class="info-box"><strong>School:</strong> <?= htmlspecialchars($student['school']) ?></div>

    <!-- 🔥 NEW SUBJECT FIELD -->
    <div class="info-box"><strong>Subjects:</strong> <?= htmlspecialchars($student['subjects'] ?? '-') ?></div>

    <div class="info-box"><strong>Total Fees:</strong> ₹<?= $student['total_fees'] ?></div>
    <div class="info-box"><strong>Total Paid:</strong> ₹<?= $total_paid ?></div>
</div>

<h4>Payment History</h4>

<table>
<tr>
    <th>Date</th>
    <th>Amount</th>
    <th>Mode</th>
    <th>Txn ID</th>
</tr>

<?php if (count($history) > 0): ?>
    <?php foreach ($history as $row): ?>
    <tr>
        <td><?= $row['payment_date'] ?></td>
        <td>₹<?= $row['amount_paid'] ?></td>
        <td><?= $row['payment_mode'] ?></td>
        <td><?= $row['transaction_id'] ?: '-' ?></td>
    </tr>
    <?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="4">No Payments Yet</td>
</tr>
<?php endif; ?>
</table>

<div class="summary">
Remaining Balance: 
<span class="balance">₹<?= $student['balance_fees'] ?></span>
</div>

<div class="signature">
    ___________________________<br>
    Authorized Signature
</div>

<div class="actions">
    <button class="btn" onclick="window.print()">Print</button>
</div>

</div>
<!-- <?php include("../includes/footer.php"); ?> -->
<script>
window.onload = function() {
    window.print();

    window.onafterprint = function(){
        window.location.href = "../Admission/admission_form.php";
    };
};
</script>
<?php
$stmt->close();
$feeStmt->close();
$conn->close();
?>