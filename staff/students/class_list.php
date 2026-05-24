<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../../config/db.php");

$selected_class = $_GET['class'] ?? '';
$students = [];
$total_balance = 0;

if ($selected_class) {
    $stmt = $conn->prepare("SELECT * FROM students WHERE `class` = ? ORDER BY roll_no ASC");
    $stmt->bind_param("s", $selected_class);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = $result->fetch_all(MYSQLI_ASSOC);

    foreach ($students as $s) {
        $total_balance += $s['balance_fees'];
    }

    $stmt->close();
}
?>

<?php include("../includes/header.php"); ?>

<!DOCTYPE html>
<html>
<head>
<title>Class Wise Student List</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="../assets/css/footer.css">
<style>
body { font-family: Arial; background:#f2f2f2; }

.container {
    width: 1100px;
    margin: 20px auto;
    background:#fff;
    padding:20px;
    border:2px solid #1E3A8A;
}

h2 { text-align:center; color:#1E3A8A; }

select, button {
    padding:6px 10px;
    margin:5px;
}

table {
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

table, th, td {
    border:1px solid #ccc;
}

th {
    background:#1E3A8A;
    color:white;
}

th, td {
    padding:8px;
    text-align:center;
}

.balance-due {
    background:#ffe5e5;
    color:red;
    font-weight:bold;
}

.balance-clear {
    background:#e6ffe6;
    color:green;
    font-weight:bold;
}

.btn {
    padding:6px 12px;
    background:#1E3A8A;
    color:white;
    text-decoration:none;
    border-radius:4px;
}

.btn:hover {
    background:#162c6b;
}

.summary {
    margin-top:15px;
    font-weight:bold;
    text-align:right;
}
</style>

</head>
<body>

<div class="container">

<h2>Class Wise Student List</h2>

<form method="GET">
    <label>Select Class:</label>
    <select name="class">
        <option value="">-- Select --</option>
        <?php
        $classes = [
            '5th',
            'navodya',
            '6th',
            '7th',
            '8th',
            '9th',
            '10th',
            '11th',
            '12th'
        ];

        foreach ($classes as $cls) {
            $selected = ($cls == $selected_class) ? 'selected' : '';
            echo "<option value='".htmlspecialchars($cls)."' $selected>$cls</option>";
        }
        ?>
    </select>
    <button type="submit">View</button>
</form>

<?php if ($selected_class && count($students) > 0): ?>

<table>
<tr>
    <th>Roll No</th>
    <th>Name</th>
    <th>School</th>
    <th>Mobile</th>
    <th>Total Fees</th>
    <th>Balance</th>
    <th>Actions</th>
</tr>

<?php foreach ($students as $row): ?>
<tr>

<td>
<a href="student_profile.php?roll_no=<?= htmlspecialchars($row['roll_no']) ?>">
<?= htmlspecialchars($row['roll_no']) ?>
</a>
</td>

<td><?= htmlspecialchars($row['full_name']) ?></td>
<td><?= htmlspecialchars($row['school']) ?></td>
<td><?= htmlspecialchars($row['mobile']) ?></td>
<td>₹<?= $row['total_fees'] ?></td>

<td class="<?= ($row['balance_fees'] > 0) ? 'balance-due' : 'balance-clear' ?>">
₹<?= $row['balance_fees'] ?>
</td>

<td>
<a class="btn" href="../fees/fee_pay.php?roll_no=<?= htmlspecialchars($row['roll_no']) ?>">Pay</a>
<a class="btn" href="../fees/receipt.php?roll_no=<?= htmlspecialchars($row['roll_no']) ?>">Receipt</a>
</td>

</tr>
<?php endforeach; ?>

</table>

<?php elseif ($selected_class): ?>
<p>No students found for this class.</p>
<?php endif; ?>

</div>
<!-- <?php include("../includes/footer.php"); ?> -->
</body>
</html>

<?php $conn->close(); ?>