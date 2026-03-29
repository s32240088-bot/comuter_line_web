<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_sekarang = $_SESSION['username'];
$query = mysqli_query($conn, "SELECT * FROM barang_temuan WHERE dilaporkan_oleh = '$user_sekarang' ORDER BY tgl_lapor DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Saya | Commuter Lost & Found</title>
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700&display=swap" rel="stylesheet">
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
        
        
        .navbar { 
            background: var(--kci-red) !important; 
            border-bottom: 5px solid var(--kci-yellow); 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .navbar-brand { font-size: 1.4rem; letter-spacing: -0.5px; }
        
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 16px; font-size: 0.83rem; transition: 0.2s; text-decoration: none; }
        
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
        
        .card-main { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); overflow: hidden; background: white; }
        .table thead { background-color: #f8f9fa; }
        .table thead th { border: none; color: #6c757d; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 20px; }
        .img-preview { width: 60px; height: 60px; object-fit: cover; border-radius: 12px; }
        .btn-action { border-radius: 10px; transition: all 0.3s; }
        .btn-action:hover { transform: translateY(-2px); }
        .status-badge { padding: 6px 12px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }

       
        .empty-state-wrapper { padding: 4rem 2rem; }
        .empty-icon {
            width: 100px; height: 100px;
            background: rgba(227, 30, 36, 0.1);
            color: var(--kci-red);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 3rem; margin: 0 auto 1.5rem;
        }
        .btn-kci {
            background: var(--kci-red);
            color: white;
            border-radius: 50px;
            padding: 10px 25px;
            font-weight: 600;
            border: none;
            transition: 0.3s;
        }
        .btn-kci:hover { background: #757d18; color: white; transform: translateY(-2px); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark sticky-top mb-5">
    <div class="container py-1">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="home_user.php">
            <i class="bi bi-train-front-fill me-2 text-warning"></i> 
            COMMUTER <span class="text-warning ms-1">L&F</span>
        </a>
        <div class="d-flex align-items-center gap-3">
            <span class="text-white small d-none d-md-block">Halo, <b><?php echo htmlspecialchars($_SESSION['username']); ?></b></span>
            <a href="home_user.php" class="btn-back">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row mb-4 align-items-end">
        <div class="col-md-6">
            <h3 class="fw-bold text-dark mb-1">Riwayat Laporan Temuan</h3>
            <p class="text-muted mb-0">Kelola semua barang yang telah Anda laporkan di sini.</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                <i class="bi bi-info-circle me-1"></i> Update Hari Ini
            </span>
        </div>
    </div>

    <div class="card card-main">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Informasi Barang</th>
                        <th>Kategori</th>
                        <th>Tanggal Lapor</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($query) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($query)): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center py-2">
                                    <img src="<?php echo !empty($row['foto']) ? 'uploads/'.htmlspecialchars($row['foto']) : 'https://via.placeholder.com/60?text=N/A'; ?>"
                                         class="img-preview me-3 shadow-sm"
                                         onerror="this.src='https://via.placeholder.com/60?text=N/A'">
                                    <div>
                                        <div class="fw-bold text-dark"><?php echo htmlspecialchars($row['nama_barang']); ?></div>
                                        <small class="text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i><?php echo htmlspecialchars($row['lokasi']); ?></small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['kategori']); ?></span></td>
                            <td class="text-muted small"><?php echo date('d F Y', strtotime($row['tgl_lapor'])); ?></td>
                            <td>
                                <?php if ($row['status'] == 'Tersedia'): ?>
                                <span class="status-badge bg-success-subtle text-success border border-success-subtle">Tersedia</span>
                                <?php else: ?>
                                <span class="status-badge bg-secondary-subtle text-secondary border border-secondary-subtle">Sudah Diambil</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="edit_laporan.php?id=<?php echo $row['id_barang']; ?>" class="btn btn-action btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="hapus_laporan.php?id=<?php echo $row['id_barang']; ?>"
                                       class="btn btn-action btn-outline-danger btn-sm"
                                       onclick="return confirm('Hapus laporan ini secara permanen?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center empty-state-wrapper">
                                <div class="empty-icon">
                                    <i class="bi bi-archive-fill"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">Belum Ada Riwayat</h5>
                                <p class="text-muted small mb-4">Sepertinya Anda belum pernah melaporkan barang temuan.</p>
                                <a href="home_user.php" class="btn btn-kci shadow-sm">
                                    <i class="bi bi-plus-circle me-2"></i>Buat Laporan Sekarang
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>