<?php
session_start();
require_once("../config/db.php");

/* =========================
   CHECK STUDENT ID
=========================*/
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Student ID");
}

$id = intval($_GET['id']);

/* =========================
   CHECK IF STUDENT EXISTS
=========================*/
$check = $conn->prepare("SELECT roll_no FROM students WHERE id = ?");
$check->bind_param("i", $id);
$check->execute();
$result = $check->get_result();
$student = $result->fetch_assoc();

if (!$student) {
    die("Student Not Found");
}

/* =========================
   DELETE RELATED FEES FIRST
=========================*/
$deleteFees = $conn->prepare("DELETE FROM fees WHERE student_id = ?");
$deleteFees->bind_param("i", $id);
$deleteFees->execute();

/* =========================
   DELETE STUDENT
=========================*/
$deleteStudent = $conn->prepare("DELETE FROM students WHERE id = ?");
$deleteStudent->bind_param("i", $id);

if ($deleteStudent->execute()) {

    header("Location: students.php?msg=deleted");
    exit();

} else {
    echo "Delete Failed!";
}

$check->close();
$deleteFees->close();
$deleteStudent->close();
$conn->close();
?>