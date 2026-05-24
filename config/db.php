<?php
$conn = new mysqli("127.0.0.1", "root", "", "unique_class_db", 3307);

if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}
?>