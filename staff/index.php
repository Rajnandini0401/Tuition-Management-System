<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'staff'){
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Tuition Management System</title>

<style>
body {
    font-family: Arial;
    background: #6e6767;
    margin: 0;
}

.container {
    width: 550px;
    margin: 80px auto;
    background: black;
    padding: 40px;
    text-align: center;
    border-radius: 8px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

.logo img {
    width: 90px;
    margin-bottom: 15px;
}

h1 {
    color: #c1d71a;
    margin-bottom: 5px;
}

p {
    color: #555;
    margin-bottom: 30px;
}

.button {
    display: block;
    width: 100%;
    padding: 14px;
    margin: 15px 0;
    background: #e18a5c;
    color: white;
    text-decoration: none;
    border-radius: 6px;
    font-size: 16px;
    transition: 0.2s ease;
}

.button:hover {
    background: #5d5d53;
    transform: scale(1.02);
}
</style>
</head>

<body>

<div class="container">

<div class="logo">
    <img src="../staff/assets/Images/logo1.png" alt="Unique Classes Logo">
</div>

<h1>UNIQUE CLASSES</h1>
<p>Tuition Management System Dashboard</p>

<a href="admission/admission_form.php" class="button">
➤ New Admission
</a>

<a href="students/class_list.php" class="button">
➤ Class Wise Student List
</a>

<a href="students/class_list.php" class="button">
➤ Search Student / Payment
</a>

</div>
<?php include("../includes/footer.php"); ?>

</body>
</html>