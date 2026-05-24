<!-- <?php
$conn = new mysqli("", "root", "", "unique_class_db", 3307);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?> -->

<?php
$conn = mysqli_connect("127.0.0.1", "root", "", "otp_login_system", 3307);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());
}
    ?>