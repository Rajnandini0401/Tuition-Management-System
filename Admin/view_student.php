<?php
session_start();
require_once("../config/db.php");

if(!isset($_GET['roll_no'])){
    die("Roll number missing");
}

$roll_no = $_GET['roll_no'];

/* ===== GET STUDENT ===== */
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no=?");
$stmt->bind_param("s", $roll_no);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if(!$student){
    die("Student not found");
}

/* ===== GET FEES ===== */
$fees = $conn->prepare("
    SELECT amount_paid, payment_mode, payment_date 
    FROM fees 
    WHERE student_id=? 
    ORDER BY payment_date DESC
");
$fees->bind_param("i", $student['id']);
$fees->execute();
$fee_result = $fees->get_result();

/* ===== TOTAL PAID ===== */
$total_paid = 0;
while($row = $fee_result->fetch_assoc()){
    $total_paid += $row['amount_paid'];
}

/* RESET POINTER */
$fee_result->data_seek(0);
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Profile</title>
<link rel="stylesheet" href="admin.css">

<style>
.profile-box{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

.profile-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
}

.profile-item{
    font-size:14px;
}

.section-title{
    font-weight:bold;
    margin-bottom:10px;
}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}

th, td{
    border:1px solid #ddd;
    padding:8px;
    text-align:center;
}

th{
    background:#1E3A8A;
    color:white;
}
</style>
</head>

<body>

<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

<h2>Student Profile</h2>

<!-- STUDENT INFO -->
<div class="profile-box">
    <div class="section-title">Basic Information</div>

    <div class="profile-grid">
        <div class="profile-item"><b>Name:</b> <?= $student['full_name'] ?></div>
        <div class="profile-item"><b>Roll No:</b> <?= $student['roll_no'] ?></div>
        <div class="profile-item"><b>Class:</b> <?= $student['class'] ?></div>
        <div class="profile-item"><b>Subjects:</b> <?= $student['subjects'] ?></div>
        <div class="profile-item"><b>Mobile:</b> <?= $student['mobile'] ?></div>
        <div class="profile-item"><b>School:</b> <?= $student['school'] ?></div>
    </div>
</div>

<!-- FEES SUMMARY -->
<div class="profile-box">
    <div class="section-title">Fees Summary</div>

    <div class="profile-grid">
        <div><b>Total Fees:</b> ₹<?= $student['total_fees'] ?></div>
        <div><b>Total Paid:</b> ₹<?= $total_paid ?></div>
        <div><b>Remaining:</b> ₹<?= $student['balance_fees'] ?></div>
    </div>
</div>

<!-- PAYMENT HISTORY -->
<div class="profile-box">
    <div class="section-title">Payment History</div>

    <table>
        <tr>
            <th>Date</th>
            <th>Amount</th>
            <th>Mode</th>
        </tr>

        <?php if($fee_result->num_rows > 0): ?>
            <?php while($row = $fee_result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['payment_date'] ?></td>
                <td>₹<?= $row['amount_paid'] ?></td>
                <td><?= $row['payment_mode'] ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No payments yet</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

</div>

</body>
</html>

<?php
$stmt->close();
$fees->close();
$conn->close();
?>