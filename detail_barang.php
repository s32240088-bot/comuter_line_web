<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_barang) {
    header("Location: home_user.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM barang_temuan WHERE id_barang = ?");
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$barang = $stmt->get_result()->fetch_assoc();

if (!$barang) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='home_user.php';</script>";
    exit();
}

$username = $_SESSION['username'];

$cek_klaim = $conn->prepare("SELECT id_klaim, status_klaim FROM klaim_barang WHERE id_barang = ? AND username_klaim = ? AND status_klaim NOT IN ('Ditolak')");
$cek_klaim->bind_param("is", $id_barang, $username);
$cek_klaim->execute();
$existing_klaim = $cek_klaim->get_result()->fetch_assoc();

$res_total = $conn->prepare("SELECT COUNT(*) as total FROM klaim_barang WHERE id_barang = ? AND status_klaim NOT IN ('Ditolak')");
$res_total->bind_param("i", $id_barang);
$res_total->execute();
$total_klaim = $res_total->get_result()->fetch_assoc()['total'];

$barang_tersedia = ($barang['status'] === 'Tersedia');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail: <?php echo htmlspecialchars($barang['nama_barang']); ?> | Commuter L&F</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --kci-red: #e31e24; --kci-yellow: #ffc107; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar-kci { background-color: var(--kci-red); border-bottom: 5px solid var(--kci-yellow); }
        .detail-card { border: none; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .img-detail { width: 100%; max-height: 450px; object-fit: cover; border-radius: 15px; }
        .info-label { font-weight: 600; color: #6c757d; font-size: 0.85rem; margin-bottom: 2px; }
        .info-value { font-weight: 700; color: #333; font-size: 1rem; margin-bottom: 16px; }
        .btn-claim { background-color: var(--kci-red); color: white; border-radius: 12px; padding: 14px; font-weight: 700; font-size: 1rem; border: none; transition: 0.3s; }
        .btn-claim:hover { background-color: #b3171b; color: white; transform: translateY(-2px); }
        .btn-claim:disabled { background-color: #adb5bd; cursor: not-allowed; transform: none; }
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; transition: 0.2s; }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-kci shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home_user.php">
            <i class="bi bi-train-front-fill me-2 text-warning"></i>COMMUTER <span class="text-warning">L&F</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small d-none d-md-block"><i class="bi bi-people me-1"></i><?php echo $total_klaim; ?> pengajuan klaim</span>
            <a href="home_user.php" class="btn btn-back">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card detail-card p-3">
                <img src="<?php echo !empty($barang['foto']) ? 'uploads/'.htmlspecialchars($barang['foto']) : 'https://via.placeholder.com/600x400?text=No+Image'; ?>"
                     class="img-detail shadow-sm" alt="Foto Barang">
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card detail-card p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                        <i class="bi bi-tag-fill me-1"></i> <?php echo $barang['kategori']; ?>
                    </span>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i> <?php echo date('d M Y', strtotime($barang['tgl_lapor'])); ?>
                    </small>
                </div>

                <h2 class="fw-bold mb-4 text-danger"><?php echo htmlspecialchars($barang['nama_barang']); ?></h2>
                <hr>

                <div class="row mt-3">
                    <div class="col-6">
                        <p class="info-label">Lokasi Penemuan</p>
                        <p class="info-value"><i class="bi bi-geo-alt-fill text-danger me-1"></i> <?php echo htmlspecialchars($barang['lokasi']); ?></p>
                    </div>
                    <div class="col-6">
                        <p class="info-label">Status Barang</p>
                        <?php if ($barang_tersedia): ?>
                            <p class="info-value text-success"><i class="bi bi-check-circle-fill me-1"></i>Tersedia</p>
                        <?php else: ?>
                            <p class="info-value text-secondary"><i class="bi bi-x-circle-fill me-1"></i>Sudah Dikembalikan</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mt-1">
                    <p class="info-label">Deskripsi Barang</p>
                    <div class="p-3 bg-light rounded-3">
                        <p class="mb-0 text-dark small"><?php echo nl2br(htmlspecialchars($barang['deskripsi'])); ?></p>
                    </div>
                </div>

                <div class="mt-4 d-grid gap-2">
                    <?php if (!$barang_tersedia): ?>
                    <button class="btn btn-claim" disabled>
                        <i class="bi bi-x-circle me-2"></i>Barang Sudah Dikembalikan
                    </button>
                    <?php elseif ($existing_klaim): ?>
                    <a href="status_klaim.php" class="btn btn-outline-success rounded-pill fw-bold py-3">
                        <i class="bi bi-clock-history me-2"></i>
                        Klaim Anda: <b><?php echo $existing_klaim['status_klaim']; ?></b> — Lihat Status
                    </a>
                    <?php else: ?>
                    <a href="ajukan_klaim.php?id=<?php echo $barang['id_barang']; ?>" class="btn btn-claim">
                        <i class="bi bi-shield-check me-2"></i>Ajukan Klaim Kepemilikan
                    </a>
                    <?php endif; ?>

                    <p class="text-center small text-muted mb-0">
                        Klaim akan diverifikasi oleh petugas. Siapkan bukti identitas Anda.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>