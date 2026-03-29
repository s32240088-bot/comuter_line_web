<?php
include "connect.php";
session_start();

if (isset($_SESSION['username'])) {
    header("Location: home_user.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $nama     = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon  = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat   = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $cek_user = mysqli_query($conn, "SELECT * FROM register_user WHERE username='$username'");

    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>alert('Username sudah digunakan! Silakan pilih yang lain.');</script>";
    } else {
        $sql = "INSERT INTO register_user (username, nama, telepon, email, alamat, password)
                VALUES ('$username', '$nama', '$telepon', '$email', '$alamat', '$password')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='login.php';</script>";
        } else {
            echo "<script>alert('Gagal mendaftar: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Commuter Lost & Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e31e24 0%, #8b1216 100%); min-height: 100vh; display: flex; flex-direction: column; margin: 0; }
        .top-bar { background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.15); padding: 12px 0; }
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
        .register-container { flex: 1; display: flex; align-items: center; justify-content: center; padding: 30px 20px; }
        .register-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.15); width: 100%; max-width: 500px; padding: 40px; }
        .btn-register { background-color: #e31e24; color: white; font-weight: bold; padding: 12px; border-radius: 10px; border: none; transition: 0.3s; }
        .btn-register:hover { background-color: #c4191f; color: white; transform: translateY(-2px); }
        .form-control { border-radius: 10px; padding: 12px; border: 1px solid #dee2e6; }
        .form-control:focus { box-shadow: none; border-color: #e31e24; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="login.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Sudah punya akun? Login
        </a>
        <span class="text-white small fw-semibold"><i class="bi bi-train-front-fill me-1 text-warning"></i>COMMUTER L&F</span>
    </div>
</div>

<div class="register-container">
    <div class="register-card">
        <div class="text-center mb-4">
            <h3 class="fw-bold" style="color: #e31e24;">BUAT AKUN BARU</h3>
            <p class="text-secondary small">Lengkapi data diri Anda untuk bergabung</p>
        </div>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Username</label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">No. Telepon</label>
                    <input type="tel" name="telepon" class="form-control" placeholder="0812..." required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label small fw-bold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@domain.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-bold">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Masukkan alamat lengkap" required></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label small fw-bold">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Buat password" required>
            </div>
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-register">
                    <i class="bi bi-person-plus me-2"></i>DAFTAR SEKARANG
                </button>
                <a href="login.php" class="btn btn-outline-secondary rounded-pill py-2 small">Sudah punya akun? Login</a>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>