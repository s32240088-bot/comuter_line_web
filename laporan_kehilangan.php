<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_laporan'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $tgl_hilang  = mysqli_real_escape_string($conn, $_POST['tgl_hilang']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $foto_file   = "";

    if (!empty($_FILES['foto']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];
        if (in_array($ext, $allowed)) {
            $foto_file = "HILANG_" . time() . "_" . uniqid() . "." . $ext;
            if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_file);
        }
    }

    $sql = "INSERT INTO laporan_kehilangan (username, nama_barang, kategori, lokasi, tgl_hilang, deskripsi, foto)
            VALUES ('$username', '$nama_barang', '$kategori', '$lokasi', '$tgl_hilang', '$deskripsi', '$foto_file')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Laporan berhasil dikirim!'); window.location='laporan_kehilangan.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}

$my_laporan = mysqli_query($conn, "SELECT lk.*, bt.nama_barang AS barang_cocok_nama
    FROM laporan_kehilangan lk
    LEFT JOIN barang_temuan bt ON lk.id_barang_cocok = bt.id_barang
    WHERE lk.username = '$username'
    ORDER BY lk.tgl_lapor DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehilangan | Commuter L&F</title>
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --kci-red: #e31e24; --kci-yellow: #ffc107; }

         body { 
            font-family: 'Poppins', sans-serif; 
    
            background-image: linear-gradient(rgba(96, 88, 88, 0.7), rgba(255, 255, 255, 0.7)), 
                              url('uploads/background.png'); 
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
        }
        

        .navbar-kci { background-color: var(--kci-red); border-bottom: 5px solid var(--kci-yellow); }
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 16px; font-size: 0.83rem; text-decoration: none; }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }

        .card-custom { 
            border: none; 
            border-radius: 15px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            background-color: rgba(255, 255, 255, 0.9); /
            backdrop-filter: blur(5px); 
        }

        .btn-merah { background-color: var(--kci-red); color: white; border-radius: 10px; font-weight: 600; border: none; }
        .btn-merah:hover { background-color: #b3171b; color: white; }
        
        .status-mencari  { background: #fff3cd; color: #856404; }
        .status-diproses { background: #cfe2ff; color: #084298; }
        .status-ditemukan{ background: #d1e7dd; color: #0a3622; }
        .badge-status { padding: 6px 14px; border-radius: 20px; font-size: 0.78rem; font-weight: 600; }
        .img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 10px; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-kci shadow-sm sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home_user.php">
            <i class="bi bi-train-front-fill me-2 text-warning"></i>COMMUTER <span class="text-warning">L&F</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small">Halo, <b><?php echo htmlspecialchars($username); ?></b></span>
            <a href="home_user.php" class="btn-back"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-megaphone-fill text-danger me-2"></i>Laporkan Kehilangan</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Dokumen">Dokumen</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label small fw-bold">Tgl Hilang</label>
                            <input type="date" name="tgl_hilang" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Lokasi Stasiun</label>
                        <input type="text" name="lokasi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Foto</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <button type="submit" name="submit_laporan" class="btn btn-merah w-100 py-2">Kirim Laporan</button>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <h5 class="fw-bold mb-3"><i class="bi bi-clock-history text-danger me-2"></i>Riwayat Laporan Saya</h5>
            <?php if (mysqli_num_rows($my_laporan) == 0): ?>
                <div class="card card-custom p-5 text-center">
                    <p class="text-muted mb-0">Belum ada laporan.</p>
                </div>
            <?php else: ?>
                <?php while($row = mysqli_fetch_assoc($my_laporan)): ?>
                <div class="card card-custom mb-3 p-3">
                    <div class="d-flex align-items-start gap-3">
                        <?php if (!empty($row['foto'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" class="img-thumb">
                        <?php else: ?>
                            <div class="img-thumb bg-light d-flex align-items-center justify-content-center"><i class="bi bi-image text-muted"></i></div>
                        <?php endif; ?>
                        
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <h6 class="fw-bold mb-0"><?php echo htmlspecialchars($row['nama_barang']); ?></h6>
                                <?php
                                $st = $row['status'];
                                $cls = $st == 'Ditemukan' ? 'status-ditemukan' : ($st == 'Diproses' ? 'status-diproses' : 'status-mencari');
                                ?>
                                <span class="badge-status <?php echo $cls; ?>"><?php echo $st; ?></span>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($row['lokasi']); ?> | <?php echo date('d M Y', strtotime($row['tgl_hilang'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>