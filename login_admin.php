<?php
include "connect.php";
session_start();

if (isset($_SESSION['username_admin'])) {
    header("Location: dashboard_admin.php");
    exit();
}

if(isset($_POST['login'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");

    if(mysqli_num_rows($query) > 0){
        $data = mysqli_fetch_assoc($query);
        $_SESSION['username_admin'] = $data['username'];
        $_SESSION['nama_admin']     = $data['nama_lengkap'];
        $_SESSION['role']           = 'admin';
        header("Location: dashboard_admin.php");
        exit();
    } else {
        echo "<script>alert('Username atau Password Admin salah!'); window.location.href='login_admin.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin | Commuter Lost & Found</title>
    
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            /* Background menggunakan gambar dengan overlay gelap agar form tetap terbaca */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                        url('uploads/kereta_background.png'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            margin: 0; 
        }

        /* Top Bar: Merah dengan aksen Kuning di bawahnya */
        .top-bar { 
            background: #e31e24; 
            border-bottom: 4px solid #ffc107; 
            padding: 12px 0; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }

        .btn-back { 
            background: #ffc107; 
            border: 1px solid #e31e24; 
            color: #000; 
            border-radius: 50px; 
            padding: 6px 18px; 
            font-size: 0.85rem; 
            transition: 0.3s; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            font-weight: 600;
        }
        .btn-back:hover { 
            background: rgba(255,255,255,0.4); 
            color: white; 
        }

        .login-wrapper { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 30px 20px; 
        }

        .login-card { 
            background: white; 
            padding: 2.5rem; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.4); 
            width: 100%; 
            max-width: 400px; 
        }

        .btn-login { 
            background: #e31e24; 
            color: white; 
            border-radius: 10px; 
            padding: 12px; 
            font-weight: 700; 
            border: none; 
            transition: 0.3s; 
            text-transform: uppercase;
        }
        .btn-login:hover { 
            background: #c1191f; 
            color: white; 
            transform: translateY(-2px); 
        }

        .form-control { 
            border-radius: 10px; 
            padding: 0.75rem 1rem; 
            background-color: #f8f9fa;
        }
        .form-control:focus { 
            box-shadow: none; 
            border-color: #e31e24; 
            background-color: #fff;
        }
        
        .form-label {
            font-size: 0.75rem;
            letter-spacing: 0.5px;
        }

        .register-link { 
            font-size: 0.85rem; 
            color: #6c757d; 
        }
        .register-link a { 
            color: #e31e24; 
            font-weight: 700; 
            text-decoration: none; 
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="login.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Login sebagai User
        </a>
        <span class="text-white small fw-bold">
            <i class="bi bi-shield-lock-fill me-1 text-warning"></i> ADMIN PANEL
        </span>
    </div>
</div>

<div class="login-wrapper">
    <div class="login-card">
        <div class="text-center mb-4">
            <div class="mb-2">
                <i class="bi bi-shield-lock-fill text-danger" style="font-size:3rem;"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1">ADMIN LOGIN</h4>
            <p class="text-secondary small">Masuk ke sistem manajemen Commuter L&F</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold text-secondary">USERNAME ADMIN</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-danger"></i></span>
                    <input type="text" name="username" class="form-control border-start-0" placeholder="Username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold text-secondary">PASSWORD</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-danger"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0" placeholder="••••••••" required>
                    <span class="input-group-text bg-light border-start-0" style="cursor: pointer;" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-login w-100 mb-3" name="login">
                <i class="bi bi-box-arrow-in-right me-2"></i>LOGIN SEKARANG
            </button>
        </form>

        <div class="text-center register-link">
            Belum terdaftar? <a href="register_admin.php">Buat Akun Admin</a>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>