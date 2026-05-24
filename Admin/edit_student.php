<?php
session_start();
require_once("../config/db.php");

/* CHECK ID */
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Student ID");
}

$id = intval($_GET['id']);

/* FETCH STUDENT */
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

if (!$student) {
    die("Student Not Found");
}

/* ===== UPDATE ===== */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name = $_POST['full_name'];
    $class = $_POST['class'];
    $school = $_POST['school'];
    $dob = $_POST['dob'];
    $mobile = $_POST['mobile'];
    $whatsapp = $_POST['whatsapp'];
    $address = $_POST['address'];

    /* NEW */
    $subjects = $_POST['subjects'];
    $total_fees = floatval($_POST['total_fees']);

    /* CALCULATE PAID */
    $paidQuery = $conn->query("
        SELECT SUM(amount_paid) as total 
        FROM fees WHERE student_id = $id
    ");
    $paid = $paidQuery->fetch_assoc()['total'] ?? 0;

    $balance = $total_fees - $paid;

    /* UPDATE */
    $update = $conn->prepare("
        UPDATE students 
        SET full_name=?, class=?, school=?, dob=?, mobile=?, whatsapp=?, address=?, subjects=?, total_fees=?, balance_fees=?
        WHERE id=?
    ");

    $update->bind_param(
        "ssssssssddi",
        $full_name,
        $class,
        $school,
        $dob,
        $mobile,
        $whatsapp,
        $address,
        $subjects,
        $total_fees,
        $balance,
        $id
    );

    if ($update->execute()) {
        header("Location: view_student.php?roll_no=" . $student['roll_no']);
        exit();
    } else {
        echo "Update Failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>
<link rel="stylesheet" href="admin.css">

<style>
.container{
    width:850px;
    margin:30px auto;
    background:white;
    padding:25px;
    border:2px solid #1E3A8A;
}
h2{text-align:center;color:#1E3A8A;}

.section{
    margin-top:20px;
    padding:15px;
    border:1px solid #ddd;
}
.section-title{
    font-weight:bold;
    margin-bottom:10px;
    color:#1E3A8A;
}

.row{display:flex; gap:20px;}
.col{flex:1;}

input, select, textarea{
    width:100%;
    padding:8px;
    margin-bottom:10px;
}

button{
    padding:10px 18px;
    background:#1E3A8A;
    color:white;
    border:none;
    cursor:pointer;
}
</style>
</head>

<body>

<?php include("includes/admin_sidebar.php"); ?>

<div class="main">

<div class="container">

<h2>Edit Student</h2>

<form method="POST">

<!-- STUDENT INFO -->
<div class="section">
<div class="section-title">Student Information</div>

<label>Full Name</label>
<input type="text" name="full_name"
value="<?= htmlspecialchars($student['full_name']) ?>" required>

<div class="row">
<div class="col">
<label>Class</label>
<select name="class" required>
<?php
$classes = ['5th','6th','7th','8th','9th','10th','11th','12th','navodya'];
foreach($classes as $cls){
    $selected = ($student['class']==$cls) ? "selected" : "";
    echo "<option value='$cls' $selected>$cls</option>";
}
?>
</select>
</div>

<div class="col">
<label>Date of Birth</label>
<input type="date" name="dob"
value="<?= $student['dob'] ?>">
</div>
</div>

<label>School</label>
<input type="text" name="school"
value="<?= htmlspecialchars($student['school']) ?>">

<label>Subjects</label>
<input type="text" name="subjects"
value="<?= htmlspecialchars($student['subjects']) ?>">

<label>Address</label>
<textarea name="address"><?= htmlspecialchars($student['address']) ?></textarea>

</div>

<!-- CONTACT -->
<div class="section">
<div class="section-title">Contact Details</div>

<div class="row">
<div class="col">
<label>Mobile</label>
<input type="text" name="mobile"
value="<?= htmlspecialchars($student['mobile']) ?>">
</div>

<div class="col">
<label>WhatsApp</label>
<input type="text" name="whatsapp"
value="<?= htmlspecialchars($student['whatsapp']) ?>">
</div>
</div>
</div>

<!-- FEES -->
<div class="section">
<div class="section-title">Fees Details</div>

<div class="row">
<div class="col">
<label>Total Fees</label>
<input type="number" name="total_fees"
value="<?= $student['total_fees'] ?>" required>
</div>

<div class="col">
<label>Current Balance</label>
<input type="number"
value="<?= $student['balance_fees'] ?>" readonly>
</div>
</div>

</div>

<div style="text-align:center; margin-top:20px;">
<button type="submit">Update Student</button>
</div>

</form>

</div>

</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>