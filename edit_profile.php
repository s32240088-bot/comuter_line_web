<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['username'];

$query = mysqli_query($conn, "SELECT * FROM register_user WHERE username='$current_user'");
$data  = mysqli_fetch_assoc($query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama    = mysqli_real_escape_string($conn, $_POST['nama']);
    $telepon = mysqli_real_escape_string($conn, $_POST['telepon']);
    $email   = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $sql = "UPDATE register_user SET
            nama='$nama',
            telepon='$telepon',
            email='$email',
            alamat='$alamat',
            password='$password'
            WHERE username='$current_user'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Profil berhasil diperbarui!'); window.location.href='home_user.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil | Commuter L&F</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --kci-red: #e31e24; --kci-yellow: #ffc107; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e31e24 0%, #8b1216 100%); min-height: 100vh; display: flex; flex-direction: column; }
        .top-bar { background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.15); padding: 12px 0; }
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
        .content-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; padding: 30px 20px; }
        .register-card { background: white; border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); width: 100%; max-width: 550px; padding: 2.5rem; position: relative; overflow: hidden; }
        .register-card::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 5px; background: var(--kci-yellow); }
        .brand-logo { color: var(--kci-red); font-weight: 700; font-size: 1.5rem; text-align: center; }
        .brand-logo span { color: var(--kci-yellow); }
        .form-control { border-radius: 10px; padding: 0.75rem 1rem; border: 1px solid #dee2e6; background-color: #f8f9fa; }
        .form-control:focus { border-color: var(--kci-red); box-shadow: 0 0 0 0.2rem rgba(227,30,36,0.1); background: white; }
        .form-control[readonly] { background-color: #e9ecef; color: #6c757d; }
        .btn-update { background-color: var(--kci-yellow); border: none; border-radius: 10px; padding: 0.8rem; font-weight: 600; color: #333; transition: 0.3s; }
        .btn-update:hover { background-color: #e3a900; transform: translateY(-2px); color: #333; }
        .avatar-wrapper { width: 70px; height: 70px; border-radius: 50%; overflow: hidden; margin: 0 auto 10px; border: 3px solid var(--kci-yellow); }
        .avatar-wrapper img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="home_user.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Home
        </a>
        <span class="text-white small fw-semibold"><i class="bi bi-person-gear me-1 text-warning"></i>Edit Profil</span>
    </div>
</div>

<div class="content-wrapper">
    <div class="register-card">
        <div class="text-center mb-4">
            <div class="avatar-wrapper">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($data['nama']); ?>&background=ffc107&color=333&size=70">
            </div>
            <div class="brand-logo mb-1">COMMUTER <span>L&F</span></div>
            <h5 class="fw-bold">Edit Profil Saya</h5>
            <p class="text-muted small">Perbarui informasi akun Anda</p>
        </div>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold small">Nama Lengkap</label>
                <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small">Username <span class="text-muted">(tidak bisa diubah)</span></label>
                    <input type="text" class="form-control" value="<?php echo htmlspecialchars($data['username']); ?>" readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small">No. Telepon</label>
                    <input type="tel" name="telepon" class="form-control" value="<?php echo htmlspecialchars($data['telepon']); ?>" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">Alamat</label>
                <textarea name="alamat" class="form-control" rows="2" required><?php echo htmlspecialchars($data['alamat']); ?></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">Password Baru</label>
                <input type="password" name="password" class="form-control" value="<?php echo htmlspecialchars($data['password']); ?>" required>
            </div>

            <div class="d-flex gap-2">
                <a href="home_user.php" class="btn btn-outline-secondary w-50 rounded-pill">Batal</a>
                <button type="submit" class="btn btn-update w-50 rounded-pill shadow-sm">
                    <i class="bi bi-floppy me-1"></i>SIMPAN PERUBAHAN
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>