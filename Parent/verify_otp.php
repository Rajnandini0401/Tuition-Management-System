<?php
session_start();

$conn = mysqli_connect("127.0.0.1", "root", "", "otp_login_system", 3307);

$mobile = $_POST['mobile'];
$otp = $_POST['otp'];

$result = mysqli_query($conn, "SELECT * FROM otp_verification 
WHERE mobile='$mobile' AND otp='$otp' ORDER BY id DESC LIMIT 1");

if(mysqli_num_rows($result) > 0){
    
    $_SESSION['mobile'] = $mobile;
    header("Location: dashboard.php");

}else{
    echo "Invalid OTP!";
}
?>