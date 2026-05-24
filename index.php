<?php
session_start();
require_once("config/db.php");

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // 🔥 ROLE BASED REDIRECT
        if($user['role'] == 'admin'){
            header("Location: admin/dashboard.php");
        }
        elseif($user['role'] == 'staff'){
            header("Location: staff/index.php");
        }
        else{
            header("Location: teacher/dashboard.php");
        }
        exit();

    } else {
        $error = "Invalid Email or Password";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login - Unique Classes</title>

<style>
body {
    margin:0;
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #1E3A8A, #4f46e5);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* MAIN CARD */
.login-box {
    width:350px;
    background:#fff;
    padding:30px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,0.2);
    animation: fadeIn 0.8s ease;
}

/* LOGO */
.logo img {
    width:80px;
    margin-bottom:10px;
}

/* TITLE */
h2 {
    margin:5px 0;
    color:#1E3A8A;
}

.quote {
    font-size:12px;
    color:#666;
    margin-bottom:20px;
}

/* INPUT */
input {
    width:100%;
    padding:10px;
    margin:8px 0;
    border:1px solid #ccc;
    border-radius:6px;
    outline:none;
    transition:0.3s;
}

input:focus {
    border-color:#1E3A8A;
    box-shadow:0 0 5px rgba(30,58,138,0.3);
}

/* BUTTON */
button {
    width:100%;
    padding:10px;
    margin-top:10px;
    background:#1E3A8A;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
    font-size:15px;
    transition:0.3s;
}

button:hover {
    background:#162c6b;
    transform:scale(1.03);
}

/* ERROR */
.error {
    color:red;
    font-size:13px;
}

/* ANIMATION */
@keyframes fadeIn {
    from {
        opacity:0;
        transform:translateY(20px);
    }
    to {
        opacity:1;
        transform:translateY(0);
    }
}
</style>

</head>

<body>

<div class="login-box">

    <div class="logo">
        <img src="logo2.jpg" alt="Logo">
    </div>

    <h2>UNIQUE CLASSES</h2>
    <div class="quote">"Proper Application of Knowledge Moves to Progress"</div>

    <?php if($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Enter Email" required>
        <input type="password" name="password" placeholder="Enter Password" required>

        <button type="submit">Login</button>
    </form>

</div>

</body>
</html>