<?php
include "connect.php";
session_start();

if(isset($_POST['reset'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

    // Cek apakah username ada di tabel register_user
    $check = mysqli_query($conn, "SELECT * FROM register_user WHERE username='$username'");
    
    if(mysqli_num_rows($check) > 0){
        // Update password
        $update = mysqli_query($conn, "UPDATE register_user SET password='$new_password' WHERE username='$username'");
        if($update){
            echo "<script>alert('Password berhasil diperbarui! Silakan login kembali.'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Username tidak ditemukan dalam sistem!'); window.location.href='lupa_password.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | Commuter Lost & Found</title>
    
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            /* Menggunakan background yang sama dengan login.php */
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('uploads/kereta_background.png'); 
            background-size: cover; 
            background-position: center; 
            background-attachment: fixed;
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            margin: 0; 
        }

        .top-bar { 
            background: rgb(255, 0, 0); 
            border-bottom: 3px solid #ffc107; 
            padding: 12px 0; 
        }

        .btn-back { 
            background: #ffc107; 
            border: 1px solid #e31e24; 
            color: #000; 
            border-radius: 50px; 
            padding: 6px 18px; 
            font-size: 0.85rem; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            font-weight: 600; 
        }

        .login-wrapper { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 40px 20px; 
        }

        .login-card { 
            background: rgba(255, 255, 255, 0.95); 
            border-radius: 24px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.5); 
            width: 100%; 
            max-width: 420px; 
            padding: 2.5rem; 
        }

        .brand-logo { 
            color: #e31e24; 
            font-weight: 700; 
            font-size: 1.8rem; 
            text-align: center; 
        }
        .brand-logo span { color: #fbc02d; }

        .form-control { 
            border-radius: 12px; 
            padding: 0.8rem 1rem; 
            border: 1px solid #dee2e6;
        }
        .form-control:focus {
            border-color: #e31e24;
            box-shadow: 0 0 0 0.25rem rgba(227, 30, 36, 0.1);
        }

        .btn-reset { 
            background: linear-gradient(45deg, #e31e24, #b31419); 
            border: none; 
            border-radius: 12px; 
            padding: 0.8rem; 
            font-weight: 600; 
            color: white; 
            transition: 0.3s;
        }
        .btn-reset:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 15px rgba(227, 30, 36, 0.3);
        }

        .footer-text { 
            font-size: 0.9rem; 
            color: #767474; 
            text-align: center; 
            margin-top: 1.5rem; 
        }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="login.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Login
        </a>
        <span class="text-white small fw-bold">RESET AKSEN</span>
    </div>
</div>

<div class="login-wrapper">
    <div class="login-card text-dark">
        <div class="text-center mb-4">
            <div class="brand-logo mb-1">RESET <span>PASSWORD</span></div>
            <p class="text-muted small">Masukkan username untuk memperbarui password Anda.</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold small">Username Terdaftar</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-person-fill text-danger"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold small">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-light"><i class="bi bi-shield-lock-fill text-danger"></i></span>
                    <input type="password" name="new_password" id="password" class="form-control" placeholder="••••••••" required>
                    <span class="input-group-text bg-light" style="cursor: pointer;" onclick="togglePassword()">
                        <i class="bi bi-eye" id="toggleIcon"></i>
                    </span>
                </div>
            </div>

            <button type="submit" class="btn btn-reset w-100" name="reset">
                <i class="bi bi-check-circle me-2"></i>UPDATE PASSWORD
            </button>
        </form>

        <div class="footer-text">
            Ingat password Anda? <a href="login.php" style="color: #e31e24; font-weight:700; text-decoration:none;">Login sekarang</a>
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