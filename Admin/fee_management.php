<?php
session_start();
require_once("../config/db.php");

/* ---------- FILTERS ---------- */
$class_filter = $_GET['class'] ?? '';
$balance_filter = $_GET['balance'] ?? '';

$where = "1=1";

if($class_filter != ''){
    $class_safe = $conn->real_escape_string($class_filter);
    $where .= " AND class = '$class_safe'";
}

if($balance_filter == 'pending'){
    $where .= " AND balance_fees > 0";
}

if($balance_filter == 'clear'){
    $where .= " AND balance_fees = 0";
}

/* ---------- FETCH STUDENTS ---------- */
$students = $conn->query("
    SELECT * FROM students
    WHERE $where
    ORDER BY balance_fees DESC
");

/* ---------- SUMMARY ---------- */
$today_total = $conn->query("
    SELECT SUM(amount_paid) as total 
    FROM fees 
    WHERE payment_date = CURDATE()
")->fetch_assoc()['total'] ?? 0;

$pending_total = $conn->query("
    SELECT SUM(balance_fees) as total 
    FROM students
")->fetch_assoc()['total'] ?? 0;


include("includes/admin_sidebar.php");
?>
<link rel="stylesheet" href="admin.css">
<style>
.main {
    margin-left:220px;
    padding:25px;
}
.card-row{
    display:flex;
    gap:20px;
    margin-bottom:20px;
}
.card{
    flex:1;
    padding:20px;
    color:white;
    border-radius:8px;
    text-align:center;
}
.blue{background:#1E88E5;}
.red{background:#E53935;}

table{
    width:100%;
    border-collapse:collapse;
    background:white;
}
table,th,td{border:1px solid #ddd;}
th{
    background:#1E3A8A;
    color:white;
}
th,td{
    padding:8px;
    text-align:center;
}
.balance-due{color:red;font-weight:bold;}
.balance-clear{color:green;font-weight:bold;}
.btn{
    padding:5px 10px;
    text-decoration:none;
    color:white;
    border-radius:4px;
}
.pay{background:#43A047;}
.view{background:#1E88E5;}
</style>

<div class="main">

<h2>Fee Management (Admin)</h2>

<!-- SUMMARY CARDS -->
<div class="card-row">
    <div class="card blue">
        <h3>₹<?= $today_total ?></h3>
        Today's Collection
    </div>

    <div class="card red">
        <h3>₹<?= $pending_total ?></h3>
        Total Pending Fees
    </div>
</div>

<!-- FILTERS -->
<form method="GET" style="margin-bottom:15px;">
<select name="class">
    <option value="">All Classes</option>
    <?php
    $classes = ['5th','6th','7th','8th','9th','10th','11th','12th','navodya'];
    foreach($classes as $cls){
        $selected = ($cls==$class_filter) ? "selected" : "";
        echo "<option value='$cls' $selected>$cls</option>";
    }
    ?>
</select>

<select name="balance">
    <option value="">All</option>
    <option value="pending" <?= ($balance_filter=='pending')?'selected':'' ?>>Pending</option>
    <option value="clear" <?= ($balance_filter=='clear')?'selected':'' ?>>Fully Paid</option>
</select>

<button type="submit">Filter</button>
</form>

<!-- STUDENT TABLE -->
<table>
<tr>
    <th>Roll</th>
    <th>Name</th>
    <th>Class</th>
    <th>Total Fees</th>
    <th>Balance</th>
    <th>Action</th>
</tr>

<?php if($students && $students->num_rows>0): ?>
<?php while($row=$students->fetch_assoc()): ?>
<tr>
    <td><?= $row['roll_no'] ?></td>
    <td><?= $row['full_name'] ?></td>
    <td><?= $row['class'] ?></td>
    <td>₹<?= $row['total_fees'] ?></td>

    <td class="<?= ($row['balance_fees']>0)?'balance-due':'balance-clear' ?>">
        ₹<?= $row['balance_fees'] ?>
    </td>

    <td>
        <a class="btn view"
           href="student_profile.php?roll_no=<?= $row['roll_no'] ?>">
           View
        </a>

        <a class="btn pay"
           href="../fees/fee_pay.php?roll_no=<?= $row['roll_no'] ?>">
           Pay
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="6">No Records Found</td>
</tr>
<?php endif; ?>

</table>

</div>

<?php $conn->close(); ?>