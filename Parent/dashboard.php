<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "otp_login_system");

if(!isset($_SESSION['mobile'])){
    header("Location: login.php");
}

$mobile = $_SESSION['mobile'];

$result = mysqli_query($conn, "SELECT * FROM students WHERE parent_mobile='$mobile'");

while($row = mysqli_fetch_assoc($result)){
    echo "Student Name: " . $row['student_name'] . "<br>";
    echo "Class: " . $row['class'] . "<br>";
    echo "Marks: " . $row['marks'] . "<br><br>";
}
?>