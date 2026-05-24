<?php
if(session_status() == PHP_SESSION_NONE){
    session_start();
}
?>

<div class="sidebar">

    <!-- TOP MENU -->
    <div class="sidebar-top">
        <h2>Admin</h2>

        <a href="dashboard.php">Dashboard</a>
        <a href="students.php">All Students</a>
        <a href="../staff/admission/admission_form.php">New Admission</a>
        <a href="fee_management.php">Fee Management</a>
        <a href="reports.php">Reports</a>
    </div>

    <!-- PROFILE -->
    <div class="profile-box" onclick="toggleMenu()">
        <div class="profile-left">
            <div class="profile-icon">
                <?= strtoupper(substr($_SESSION['admin_name'] ?? 'A',0,1)) ?>
            </div>
            <div>
                <div class="profile-name">
                    <?= $_SESSION['admin_name'] ?? 'Admin' ?>
                </div>
                <div class="profile-role">Admin</div>
            </div>
        </div>
    </div>

    <!-- DROPDOWN -->
    <div id="profileMenu" class="profile-menu">
        <a href="logout.php">Logout</a>
    </div>

</div>

<script>
function toggleMenu(){
    let menu = document.getElementById("profileMenu");
    menu.style.display = menu.style.display === "block" ? "none" : "block";
}
</script>