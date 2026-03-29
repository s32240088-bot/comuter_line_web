<?php
include "connect.php";
session_start();

if (isset($_SESSION['username'])) {
    header("Location: home_user.php");
    exit();
}

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Mengambil data user berdasarkan username
    $query = mysqli_query($conn, "SELECT * FROM register_user WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($query) > 0){
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username'] = $data['username'];
        header("Location: home_user.php");
        exit();
    } else {
        echo "<script>alert('Username atau Password salah!'); window.location.href='login.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Commuter Lost & Found</title>
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), 
                        url('uploads/kereta_background.png'); 
            background-size: cover; background-position: center; background-attachment: fixed;
            min-height: 100vh; display: flex; flex-direction: column; margin: 0; 
        }
        .top-bar { background: rgb(255, 0, 0); border-bottom: 2px solid #ffc107; padding: 12px 0; }
        .btn-back { background: #ffc107; border: 1px solid #e31e24; color: #000; border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-weight: 600; }
        .login-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 40px 20px; }
        .login-card { background: rgba(255, 255, 255, 0.95); border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,0.4); width: 100%; max-width: 420px; padding: 2.5rem; }
        .brand-logo { color: #e31e24; font-weight: 700; font-size: 1.8rem; text-align: center; }
        .brand-logo span { color: #fbc02d; }
        .form-control { border-radius: 12px; padding: 0.8rem 1rem; }
        .btn-login { background: linear-gradient(45deg, #e31e24, #b31419); border: none; border-radius: 12px; padding: 0.8rem; font-weight: 600; color: white; }
        .footer-text { font-size: 0.9rem; color: #767474; text-align: center; margin-top: 1.5rem; }
        .forgot-link { font-size: 0.8rem; color: #e31e24; text-decoration: none; float: right; margin-bottom: 10px; }
        .forgot-link:hover { text-decoration: underline; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="home_user.php" class="btn-back"><i class="bi bi-house-door"></i> Home</a>
        <span class="text-white small fw-bold">COMMUTER L&F</span>
    </div>
</div>

<div class="login-wrapper">
    <div class="login-card text-dark">
        <div class="text-center mb-4">
            <div class="brand-logo">COMMUTER <span>L&F</span></div>
            <p class="text-muted small">Silakan masuk ke akun Anda.</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Username Anda" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold small">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="password" id="password" class="form-control" placeholder="••••••••" required>
                    <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                </div>
                <a href="lupa_password.php" class="forgot-link mt-2">Lupa password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3" name="login">LOGIN SEKARANG</button>
        </form>

        <div class="footer-text">
            Belum punya akun? <a href="register.php" style="color: #e31e24; font-weight:700; text-decoration:none;">Daftar akun</a>
        </div>
        
        <div class="text-center mt-3 pt-3 border-top">
            <a href="login_admin.php" style="color: #e31e24; font-weight:700; text-decoration:none;" class="small">login sebagai admin</a>
        </div>
    </div>
</div>

<script>
function togglePassword() {
    const passwordField = document.getElementById("password");
    const toggleIcon    = document.getElementById("toggleIcon");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        toggleIcon.classList.replace("bi-eye", "bi-eye-slash");
    } else {
        passwordField.type = "password";
        toggleIcon.classList.replace("bi-eye-slash", "bi-eye");
    }
}
</script>
</body>
</html>