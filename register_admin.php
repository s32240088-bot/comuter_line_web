<?php
include "connect.php";
session_start();

if (isset($_SESSION['username_admin'])) {
    header("Location: dashboard_admin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username     = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $no_telepon   = mysqli_real_escape_string($conn, $_POST['no_telepon']);
    $email        = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat       = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password     = mysqli_real_escape_string($conn, $_POST['password']);

    $cek_admin = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username'");

    if (mysqli_num_rows($cek_admin) > 0) {
        echo "<script>alert('Username Admin sudah digunakan!');</script>";
    } else {
        $sql = "INSERT INTO admin (username, password, nama_lengkap, No_telepon, Alamat, Email, role)
                VALUES ('$username', '$password', '$nama_lengkap', '$no_telepon', '$alamat', '$email', 'petugas')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Registrasi Admin Berhasil! Silakan Login.'); window.location.href='login_admin.php';</script>";
        } else {
            echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Admin | Commuter Lost & Found</title>

    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { 
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
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

        .top-bar { 
            background: #ff0000; 
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
            transition: 0.3s; 
            text-decoration: none; 
            display: inline-flex; 
            align-items: center; 
            gap: 6px; 
            font-weight: 600;
        }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
        
        .register-wrapper { 
            flex: 1; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 40px 20px; 
        }

        /* Card Putih Solid sesuai permintaan terakhir */
        .register-card { 
            background: #ffffff; 
            padding: 2.5rem; 
            border-radius: 20px; 
            box-shadow: 0 15px 35px rgba(0,0,0,0.4); 
            width: 100%; 
            max-width: 550px; 
        }

        .brand-title { 
            color: #e31e24; 
            font-weight: 800; 
            font-size: 1.5rem;
            margin-bottom: 5px;
            text-align: center;
        }

        .btn-register { 
            background: #e31e24; 
            color: white; 
            border-radius: 50px; 
            padding: 12px; 
            font-weight: 700; 
            border: none; 
            transition: all 0.3s; 
            text-transform: uppercase;
        }
        .btn-register:hover { 
            background: #c1191f; 
            transform: translateY(-2px); 
            color: white; 
        }

        .form-control { 
            border-radius: 10px; 
            padding: 12px; 
            background-color: #ffffff; 
            border: 1px solid #ced4da; 
        }
        .form-control:focus { 
            box-shadow: none; 
            border-color: #e31e24; 
        }
        .form-label { font-size: 0.85rem; font-weight: 700; color: #333; margin-bottom: 4px; }
        
        textarea.form-control { resize: none; height: 80px; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="login_admin.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Login Admin
        </a>
        <span class="text-white small fw-semibold"><i class="bi bi-shield-lock-fill me-1 text-warning"></i>Admin Panel</span>
    </div>
</div>

<div class="register-wrapper">
    <div class="register-card">
        <div class="text-center mb-4">
            <h3 class="brand-title">DAFTAR ADMIN</h3>
            <p class="text-secondary small">Lengkapi data untuk akses panel manajemen</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username admin" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="tel" name="no_telepon" class="form-control" placeholder="08xxxxxxxx" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@commuter.id" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" placeholder="Masukkan alamat lengkap admin" required></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Buat password admin" required>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-register">
                    <i class="bi bi-shield-check me-2"></i>DAFTAR SEKARANG
                </button>
                <a href="login_admin.php" class="btn btn-outline-secondary rounded-pill py-2 small text-decoration-none text-center">Batal dan Kembali</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>