<?php
session_start();

if($_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}
?>
<?php

require_once("../config/db.php");

function getValue($conn, $sql, $field='total'){
    $res = $conn->query($sql);
    if($res && $row = $res->fetch_assoc()){
        return $row[$field] ?? 0;
    }
    return 0;
}

$total_students = getValue($conn,"SELECT COUNT(*) as total FROM students");

$today_admission = getValue($conn,"
    SELECT COUNT(*) as total 
    FROM students 
    WHERE admission_date = CURDATE()
");

$today_collection = getValue($conn,"
    SELECT SUM(amount_paid) as total 
    FROM fees 
    WHERE payment_date = CURDATE()
");

$total_remaining = getValue($conn,"
    SELECT SUM(balance_fees) as total 
    FROM students
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<link rel="stylesheet" href="admin.css">
</head>

<body>

<!-- ✅ ONLY ONCE -->
<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

<div class="topbar">
    <h2>Dashboard Overview</h2>
    <div><?= date("d M Y") ?></div>
</div>

<div class="cards">

<a href="students.php" class="card blue">
    <h3><?= $total_students ?></h3>
    Total Students
</a>

<div class="card green">
    <h3><?= $today_admission ?></h3>
    Today's Admission
</div>

<div class="card orange">
    <h3>₹<?= $today_collection ?></h3>
    Today's Collection
</div>

<div class="card red">
    <h3>₹<?= $total_remaining ?></h3>
    Remaining Fees
</div>

</div>

</div>

</body>
</html>

<?php $conn->close(); ?>