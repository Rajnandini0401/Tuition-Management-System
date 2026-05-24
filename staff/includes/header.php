<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<style>
.navbar {
    background: #1E3A8A;
    padding: 14px;
    text-align: center;
}

.navbar a {
    color: white;
    text-decoration: none;
    margin: 0 18px;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 4px;
}

.navbar a:hover {
    background: #162c6b;
}

.active {
    background: #162c6b;
}

</style>

<div class="navbar">

    <a href="../index.php"
       class="<?= ($current_page == 'index.php') ? 'active' : '' ?>">
       Dashboard
    </a>

    <a href="../admission/admission_form.php"
       class="<?= ($current_page == 'admission_form.php') ? 'active' : '' ?>">
       New Admission
    </a>

    <a href="../students/class_list.php"
       class="<?= ($current_page == 'class_list.php') ? 'active' : '' ?>">
       Class List
    </a>

</div>