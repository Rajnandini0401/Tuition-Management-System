<?php
session_start();
require_once("../config/db.php");

/* ---------- FILTERS ---------- */
$search = $_GET['search'] ?? '';
$class_filter = $_GET['class'] ?? '';

$where = "1=1";

if($search != ''){
    $search_safe = $conn->real_escape_string($search);
    $where .= " AND (full_name LIKE '%$search_safe%' 
                OR roll_no LIKE '%$search_safe%')";
}

if($class_filter != ''){
    $class_safe = $conn->real_escape_string($class_filter);
    $where .= " AND class = '$class_safe'";
}

/* ---------- FETCH STUDENTS ---------- */
$students = $conn->query("
    SELECT * FROM students
    WHERE $where
    ORDER BY id DESC
");

?>

<!DOCTYPE html>
<html>
<head>
<title>All Students - Admin</title>
<link rel="stylesheet" href="admin.css">

<style>
body{
    margin:0;
    font-family:Arial;
    background:#f4f6f9;
}
.wrapper{display:flex;}


/* Main */
.main{
    margin-left:240px;  /* sidebar width match karo */
    padding:25px;
}

/* Filter */
.filter-box{
    background:white;
    padding:15px;
    margin-bottom:20px;
    border-radius:6px;
    box-shadow:0 0 5px rgba(0,0,0,0.05);
}
.filter-box input,
.filter-box select{
    padding:6px;
    margin-right:10px;
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
}
table, th, td{
    border:1px solid #ddd;
}
th{
    background:#1E3A8A;
    color:white;
}
th, td{
    padding:8px;
    text-align:center;
}

.balance-due{
    color:red;
    font-weight:bold;
}
.balance-clear{
    color:green;
    font-weight:bold;
}

.btn{
    padding:5px 10px;
    text-decoration:none;
    border-radius:4px;
    color:white;
    font-size:13px;
}
.view{background:#1E88E5;}
.edit{background:#FB8C00;}
.delete{background:#E53935;}

@media(max-width:768px){
    .wrapper{flex-direction:column;}
    .sidebar{width:100%;}
}
</style>
</head>

<body>
 <?php include("includes/admin_sidebar.php"); ?>

<!-- MAIN CONTENT -->
<div class="main">

<h2>All Students</h2>

<!-- FILTER -->
<div class="filter-box">
<form method="GET">
    <input type="text" name="search"
        placeholder="Search Name / Roll"
        value="<?= htmlspecialchars($search) ?>">

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

    <button type="submit">Filter</button>
</form>
</div>

<table>
<tr>
    <th>Roll</th>
    <th>Name</th>
    <th>Class</th>
    <th>Mobile</th>
    <th>Total Fees</th>
    <th>Balance</th>
    <th>Actions</th>
</tr>

<?php if($students && $students->num_rows > 0): ?>
<?php while($row = $students->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['roll_no']) ?></td>
    <td><?= htmlspecialchars($row['full_name']) ?></td>
    <td><?= htmlspecialchars($row['class']) ?></td>
    <td><?= htmlspecialchars($row['mobile']) ?></td>
    <td>₹<?= $row['total_fees'] ?></td>

    <td class="<?= ($row['balance_fees'] > 0) ? 'balance-due' : 'balance-clear' ?>">
        ₹<?= $row['balance_fees'] ?>
    </td>

    <td>
<a class="btn view"
   href="../staff/students/student_profile.php?roll_no=<?= $row['roll_no'] ?>">
   View
</a>
        <a class="btn edit"
           href="edit_student.php?id=<?= $row['id'] ?>">
           Edit
        </a>

        <a class="btn delete"
           href="delete_student.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Delete this student?')">
           Delete
        </a>
    </td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="7">No Students Found</td>
</tr>
<?php endif; ?>

</table>

</div>
</div>

</body>
</html>

<?php $conn->close(); ?>