<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username  = $_SESSION['username'];
$klaim_list = mysqli_query($conn, "
    SELECT k.*, b.nama_barang, b.kategori, b.lokasi, b.foto AS foto_barang
    FROM klaim_barang k
    JOIN barang_temuan b ON k.id_barang = b.id_barang
    WHERE k.username_klaim = '$username'
    ORDER BY k.tgl_klaim DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Klaim Saya | Commuter L&F</title>
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --kci-red: #e31e24; }
          body { 
            font-family: 'Poppins', sans-serif; 
    
            background-image: linear-gradient(rgba(96, 88, 88, 0.7), rgba(255, 255, 255, 0.7)), 
                              url('uploads/background.png'); 
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
        }
        .navbar-kci { background-color: var(--kci-red); border-bottom: 5px solid #ffc107; }
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 16px; font-size: 0.83rem; transition: 0.2s; text-decoration: none; }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
        .card-custom { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
        .status-menunggu   { background:#fff3cd; color:#856404; }
        .status-diverifikasi { background:#cfe2ff; color:#084298; }
        .status-diserahkan { background:#d1e7dd; color:#0a3622; }
        .status-ditolak    { background:#f8d7da; color:#842029; }
        .badge-status { padding:6px 16px; border-radius:20px; font-size:0.8rem; font-weight:700; display:inline-block; }
        .img-thumb { width:70px; height:70px; object-fit:cover; border-radius:12px; }
        .step-bar { display:flex; gap:0; }
        .step { flex:1; text-align:center; padding:8px 4px; font-size:0.7rem; font-weight:600; color:#adb5bd; border-bottom: 3px solid #e9ecef; }
        .step.done   { color:#0a3622; border-color:#198754; }
        .step.active { color:#e31e24; border-color:#e31e24; }
    </style>
</head>
<body>
<nav class="navbar navbar-dark navbar-kci shadow-sm sticky-top mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="home_user.php">
            <i class="bi bi-train-front-fill me-2 text-warning"></i>COMMUTER <span class="text-warning">L&F</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small d-none d-md-block">Halo, <b><?php echo htmlspecialchars($username); ?></b></span>
            <a href="home_user.php" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <h4 class="fw-bold mb-1"><i class="bi bi-clipboard2-check-fill text-danger me-2"></i>Status Klaim Saya</h4>
    <p class="text-muted small mb-4">Pantau status pengajuan klaim barang temuan Anda.</p>

    <?php if (mysqli_num_rows($klaim_list) == 0): ?>
    <div class="card card-custom p-5 text-center">
        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
        <p class="text-muted mb-3">Anda belum pernah mengajukan klaim barang.</p>
        <a href="home_user.php" class="btn btn-danger rounded-pill px-4">Cari Barang Temuan</a>
    </div>
    <?php else: ?>
        <?php while($row = mysqli_fetch_assoc($klaim_list)):
            $st    = $row['status_klaim'];
            $stMap = ['Menunggu'=>0,'Diverifikasi'=>1,'Diserahkan'=>2,'Ditolak'=>-1];
            $stIdx = $stMap[$st] ?? 0;
        ?>
        <div class="card card-custom mb-4 p-4">
            <div class="d-flex align-items-start gap-3 mb-3">
                <img src="<?php echo !empty($row['foto_barang']) ? 'uploads/'.htmlspecialchars($row['foto_barang']) : 'https://via.placeholder.com/70?text=N/A'; ?>"
                     class="img-thumb flex-shrink-0"
                     onerror="this.src='https://via.placeholder.com/70?text=N/A'">
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-1"><?php echo htmlspecialchars($row['nama_barang']); ?></h6>
                            <small class="text-muted">
                                <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($row['lokasi']); ?> &nbsp;|&nbsp;
                                <i class="bi bi-tag me-1"></i><?php echo $row['kategori']; ?>
                            </small>
                        </div>
                        <?php $cls = 'status-'.strtolower($st); ?>
                        <span class="badge-status <?php echo $cls; ?>"><?php echo $st; ?></span>
                    </div>
                </div>
            </div>

            <?php if ($st !== 'Ditolak'): ?>
            <div class="step-bar mb-3">
                <div class="step <?php echo $stIdx >= 0 ? ($stIdx == 0 ? 'active' : 'done') : ''; ?>">
                    <i class="bi bi-send d-block mb-1"></i>Diajukan
                </div>
                <div class="step <?php echo $stIdx >= 1 ? ($stIdx == 1 ? 'active' : 'done') : ''; ?>">
                    <i class="bi bi-shield-check d-block mb-1"></i>Diverifikasi
                </div>
                <div class="step <?php echo $stIdx >= 2 ? 'done' : ''; ?>">
                    <i class="bi bi-box-seam d-block mb-1"></i>Diserahkan
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-danger py-2 px-3 small rounded-3 mb-3">
                <i class="bi bi-x-circle-fill me-2"></i><strong>Klaim Ditolak</strong>
                <?php if (!empty($row['catatan_petugas'])): ?>
                — <?php echo htmlspecialchars($row['catatan_petugas']); ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="row small text-muted g-2">
                <div class="col-md-4">
                    <i class="bi bi-person me-1"></i><b>Nama Pemilik:</b> <?php echo htmlspecialchars($row['nama_pemilik']); ?>
                </div>
                <div class="col-md-4">
                    <i class="bi bi-calendar3 me-1"></i><b>Diajukan:</b> <?php echo date('d M Y', strtotime($row['tgl_klaim'])); ?>
                </div>
                <?php if ($row['tgl_verifikasi']): ?>
                <div class="col-md-4">
                    <i class="bi bi-calendar-check me-1"></i><b>Diverifikasi:</b> <?php echo date('d M Y', strtotime($row['tgl_verifikasi'])); ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($row['catatan_petugas']) && $st !== 'Ditolak'): ?>
            <div class="alert alert-info py-2 px-3 small rounded-3 mt-3 mb-0">
                <i class="bi bi-chat-left-text me-2"></i><b>Catatan Petugas:</b> <?php echo htmlspecialchars($row['catatan_petugas']); ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>