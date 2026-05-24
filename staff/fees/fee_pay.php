<?php
session_start();
require_once("../../config/db.php");

if (!isset($_GET['roll_no']) || empty($_GET['roll_no'])) {
    die("Roll Number Missing");
}

$roll_no = $_GET['roll_no'];

/* FETCH STUDENT */
$stmt = $conn->prepare("SELECT * FROM students WHERE roll_no = ?");
$stmt->bind_param("s", $roll_no);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student Not Found");
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Fee Payment</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/footer.css">

<style>
body {
    font-family: Arial;
    background:#f2f2f2;
    margin:0;
}

/* MAIN CONTAINER */
.container {
    width: 190mm;
    margin: 20px auto;
    background: #fff;
    border: 4px solid #959525;
    padding: 20px;
    box-sizing: border-box;
}

/* HEADER */
.header {
    position: relative;
    text-align: center;
    border-bottom: 3px solid #1E3A8A;
    padding-bottom: 12px;
    margin-bottom: 15px;
}

.logo img {
    width: 70px;
    position: absolute;
    left: 0;
    top: 0;
}

.header-right {
    position: absolute;
    right: 0;
    top: 0;
    text-align: right;
    font-size: 13px;
}

.header h1 {
    margin: 0;
    color: #1E3A8A;
}

.tagline {
    font-size: 12px;
    color: #555;
}

/* TITLE */
.title {
    background: #1E3A8A;
    color: #fff;
    text-align: center;
    padding: 6px;
    margin-bottom: 15px;
    font-weight: bold;
}

/* SECTION */
.section {
    border: 2px solid #D4AF37;
    padding: 12px;
    margin-bottom: 15px;
}

.section-title {
    background: #1E3A8A;
    color: white;
    padding: 4px 10px;
    display: inline-block;
    margin-bottom: 10px;
}

.row {
    display: flex;
    gap: 20px;
}

.col {
    flex: 1;
}

/* INPUT */
input, select {
    width: 100%;
    padding: 6px;
    margin-bottom: 10px;
}

/* BALANCE */
.balance-due { color:red; font-weight:bold; }
.balance-clear { color:green; font-weight:bold; }

/* BUTTON */
.btn {
    padding: 10px 15px;
    background: #1E3A8A;
    color: white;
    border: none;
    cursor: pointer;
}

/* PRINT */
@media print {
    .btn { display:none; }
}
</style>

<script>
function toggleTransaction(){
    let mode = document.getElementById("payment_mode").value;
    let box = document.getElementById("transaction_box");
    box.style.display = (mode === "Online") ? "block" : "none";
}
</script>

</head>

<body>

<div class="container">

<!-- HEADER -->
<div class="header">
    <div class="logo">
        <img src="../../logo2.jpg">
    </div>

    <div class="header-right">
        <div><strong>Date:</strong> <?= date("d/m/Y"); ?></div>
        <div><strong>Roll No:</strong> <?= $student['roll_no']; ?></div>
    </div>

    <h1>UNIQUE CLASSES</h1>
    <div class="tagline">Proper Application of Knowledge Moves to Progress</div>
</div>

<div class="title">FEE PAYMENT</div>

<!-- STUDENT INFO -->
<div class="section">
<div class="section-title">Student Information</div>

<div class="row">
    <div class="col"><b>Name:</b> <?= $student['full_name'] ?></div>
    <div class="col"><b>Class:</b> <?= $student['class'] ?></div>
</div>

<div><b>Subjects:</b> <?= $student['subjects'] ?></div>

<div>
<b>Current Balance:</b>
<span class="<?= ($student['balance_fees'] > 0)?'balance-due':'balance-clear' ?>">
₹<?= $student['balance_fees'] ?>
</span>
</div>

</div>

<!-- PAYMENT -->
<?php if($student['balance_fees'] > 0): ?>

<form method="POST" action="save_fee.php">

<input type="hidden" name="roll_no" value="<?= $student['roll_no'] ?>">

<div class="section">
<div class="section-title">Payment Details</div>

<label>Amount Paid</label>
<input type="number" name="amount_paid" required
max="<?= $student['balance_fees'] ?>">

<label>Payment Mode</label>
<select name="payment_mode" id="payment_mode" onchange="toggleTransaction()" required>
    <option value="">Select</option>
    <option>Cash</option>
    <option>Online</option>
</select>

<div id="transaction_box" style="display:none;">
<label>Transaction ID</label>
<input type="text" name="transaction_id">
</div>

</div>

<div style="text-align:center;">
    <button class="btn">Submit Payment</button>

    <a href="../students/student_profile.php?roll_no=<?= $student['roll_no'] ?>"
       class="btn" style="text-decoration:none;">
       Back
    </a>
</div>

</form>

<?php else: ?>

<div style="text-align:center; color:green; font-weight:bold;">
    All Fees Cleared ✔
</div>

<?php endif; ?>

</div>
<!-- <?php include("../includes/footer.php"); ?> -->
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>