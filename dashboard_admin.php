<?php
include "connect.php";
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login_admin.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $id_barang   = (int)$_POST['id_barang'];
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "UPDATE barang_temuan SET status='$status_baru' WHERE id_barang=$id_barang");
    echo "<script>alert('Status barang diperbarui!'); window.location='dashboard_admin.php';</script>";
    exit();
}

if (isset($_GET['hapus_barang'])) {
    $id_del = (int)$_GET['hapus_barang'];
    $res = mysqli_query($conn, "SELECT foto FROM barang_temuan WHERE id_barang=$id_del");
    $del_data = mysqli_fetch_assoc($res);
    if ($del_data && !empty($del_data['foto']) && file_exists("uploads/".$del_data['foto'])) {
        unlink("uploads/".$del_data['foto']);
    }
    mysqli_query($conn, "DELETE FROM barang_temuan WHERE id_barang=$id_del");
    echo "<script>alert('Data barang dihapus!'); window.location='dashboard_admin.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_laporan'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $admin_name  = $_SESSION['nama_admin'];

    $foto = "";
    if (!empty($_FILES['foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png'])) {
            $foto = "ADMIN_" . time() . "." . $ext;
            if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto);
        }
    }

    $sql = "INSERT INTO barang_temuan (nama_barang, kategori, lokasi, deskripsi, foto, tgl_lapor, dilaporkan_oleh, status)
            VALUES ('$nama_barang', '$kategori', '$lokasi', '$deskripsi', '$foto', NOW(), 'Admin: $admin_name', 'Tersedia')";
    mysqli_query($conn, $sql);
    echo "<script>alert('Barang temuan berhasil ditambahkan!'); window.location='dashboard_admin.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verif_klaim'])) {
    $id_klaim = (int)$_POST['id_klaim'];
    $action   = $_POST['action'];
    $catatan  = mysqli_real_escape_string($conn, $_POST['catatan_petugas']);

    if ($action == 'verifikasi') {
        mysqli_query($conn, "UPDATE klaim_barang SET status_klaim='Diverifikasi', catatan_petugas='$catatan', tgl_verifikasi=NOW() WHERE id_klaim=$id_klaim");
        echo "<script>alert('Klaim berhasil diverifikasi!'); window.location='dashboard_admin.php?tab=klaim';</script>";
    } elseif ($action == 'tolak') {
        mysqli_query($conn, "UPDATE klaim_barang SET status_klaim='Ditolak', catatan_petugas='$catatan', tgl_verifikasi=NOW() WHERE id_klaim=$id_klaim");
        echo "<script>alert('Klaim ditolak.'); window.location='dashboard_admin.php?tab=klaim';</script>";
    } elseif ($action == 'serahkan') {
        $res_klaim  = mysqli_query($conn, "SELECT id_barang, id_laporan FROM klaim_barang WHERE id_klaim=$id_klaim");
        $klaim_data = mysqli_fetch_assoc($res_klaim);
        $id_brg     = $klaim_data['id_barang'];
        $id_lap     = $klaim_data['id_laporan'];

        mysqli_query($conn, "UPDATE klaim_barang SET status_klaim='Diserahkan', catatan_petugas='$catatan', tgl_verifikasi=NOW() WHERE id_klaim=$id_klaim");
        mysqli_query($conn, "UPDATE barang_temuan SET status='Diambil' WHERE id_barang=$id_brg");
        mysqli_query($conn, "UPDATE klaim_barang SET status_klaim='Ditolak', catatan_petugas='Barang telah diserahkan ke pemilik lain.' WHERE id_barang=$id_brg AND id_klaim != $id_klaim AND status_klaim NOT IN ('Diserahkan','Ditolak')");
        if ($id_lap) {
            mysqli_query($conn, "UPDATE laporan_kehilangan SET status='Ditemukan', id_barang_cocok=$id_brg WHERE id_laporan=$id_lap");
        }
        echo "<script>alert('Barang berhasil diserahkan! Status diperbarui.'); window.location='dashboard_admin.php?tab=klaim';</script>";
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cocokkan'])) {
    $id_laporan = (int)$_POST['id_laporan'];
    $id_barang  = (int)$_POST['id_barang_cocok'];
    mysqli_query($conn, "UPDATE laporan_kehilangan SET status='Diproses', id_barang_cocok=$id_barang WHERE id_laporan=$id_laporan");
    echo "<script>alert('Laporan berhasil dicocokkan dengan barang temuan!'); window.location='dashboard_admin.php?tab=kehilangan';</script>";
    exit();
}

$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'barang';
$query_barang      = mysqli_query($conn, "SELECT * FROM barang_temuan ORDER BY tgl_lapor DESC");
$query_klaim       = mysqli_query($conn, "SELECT k.*, b.nama_barang, b.kategori, b.lokasi, b.foto AS foto_barang FROM klaim_barang k JOIN barang_temuan b ON k.id_barang=b.id_barang ORDER BY k.tgl_klaim DESC");
$query_kehilangan  = mysqli_query($conn, "SELECT * FROM laporan_kehilangan ORDER BY tgl_lapor DESC");
// FIX: barang aktif = status 'Tersedia'
$query_barang_aktif = mysqli_query($conn, "SELECT * FROM barang_temuan WHERE status='Tersedia' ORDER BY nama_barang ASC");

$total_barang  = mysqli_num_rows(mysqli_query($conn, "SELECT id_barang FROM barang_temuan"));
$total_klaim   = mysqli_num_rows(mysqli_query($conn, "SELECT id_klaim FROM klaim_barang WHERE status_klaim='Menunggu'"));
$total_selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id_barang FROM barang_temuan WHERE status='Diambil'"));
$total_hilang  = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan_kehilangan WHERE status='Mencari'"));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Commuter Lost & Found</title>
    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --red:#e31e24; --dark-red:#9b1418; }
       body { 
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), 
                        url('uploads/background_admin.png'); 
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            font-family: 'Plus Jakarta Sans', sans-serif; 
            min-height: 100vh; 
            display: flex; 
            flex-direction: column; 
            margin: 0;
        }
        .navbar { background: var(--red) !important; border-bottom: 4px solid #ffc107; }
        .stat-card { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); transition: 0.2s; }
        .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0,0,0,0.1); }
        .nav-tabs .nav-link { color: #555; font-weight: 600; border: none; border-bottom: 3px solid transparent; padding: 10px 20px; }
        .nav-tabs .nav-link.active { color: var(--red); border-bottom: 3px solid var(--red); background: transparent; }
        .nav-tabs .nav-link:hover:not(.active) { color: var(--red); border-bottom: 3px solid #f8d7da; }
        .card-main { border: none; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.07); }
        .img-thumb { width: 55px; height: 55px; object-fit: cover; border-radius: 10px; }
        .badge-status { padding: 5px 12px; border-radius: 15px; font-size: 0.75rem; font-weight: 700; display: inline-block; }
        .status-menunggu { background:#fff3cd; color:#856404; }
        .status-diverifikasi { background:#cfe2ff; color:#084298; }
        .status-diserahkan { background:#d1e7dd; color:#0a3622; }
        .status-ditolak { background:#f8d7da; color:#842029; }
        .status-tersedia { background:#d1e7dd; color:#0a3622; }
        .status-diambil { background:#e2e3e5; color:#41464b; }
        .status-mencari { background:#fff3cd; color:#856404; }
        .status-diproses { background:#cfe2ff; color:#084298; }
        .status-ditemukan { background:#d1e7dd; color:#0a3622; }
        .btn-sm-action { border-radius: 8px; font-size: 0.78rem; }
        table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6c757d; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard_admin.php">
            <i class="bi bi-shield-lock-fill me-2"></i>ADMIN PANEL
        </a>
        <div class="d-flex align-items-center gap-3 ms-auto">
            <span class="text-white small d-none d-md-block">Halo, <b><?php echo htmlspecialchars($_SESSION['nama_admin']); ?></b></span>
            <a href="logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3" onclick="return confirm('Yakin ingin keluar?')">
                <i class="bi bi-box-arrow-right me-1"></i>Keluar
            </a>
        </div>
    </div>
</nav>

<div class="container pb-5">

    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="fs-2 fw-bold text-danger"><?php echo $total_barang; ?></div>
                <div class="small text-muted">Total Temuan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="fs-2 fw-bold text-warning"><?php echo $total_klaim; ?></div>
                <div class="small text-muted">Klaim Menunggu</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="fs-2 fw-bold text-success"><?php echo $total_selesai; ?></div>
                <div class="small text-muted">Barang Dikembalikan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3 text-center">
                <div class="fs-2 fw-bold text-primary"><?php echo $total_hilang; ?></div>
                <div class="small text-muted">Lap. Kehilangan</div>
            </div>
        </div>
    </div>

    <!-- TABS -->
    <ul class="nav nav-tabs border-0 mb-4 bg-white rounded-3 px-2 shadow-sm" id="adminTab">
        <li class="nav-item">
            <a class="nav-link <?php echo $active_tab=='barang'?'active':''; ?>" href="?tab=barang">
                <i class="bi bi-box-seam me-1"></i>Barang Temuan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $active_tab=='tambah'?'active':''; ?>" href="?tab=tambah">
                <i class="bi bi-plus-circle me-1"></i>Tambah Temuan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $active_tab=='klaim'?'active':''; ?>" href="?tab=klaim">
                <i class="bi bi-person-check me-1"></i>Verifikasi Klaim
                <?php if($total_klaim>0): ?><span class="badge bg-danger ms-1"><?php echo $total_klaim; ?></span><?php endif; ?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo $active_tab=='kehilangan'?'active':''; ?>" href="?tab=kehilangan">
                <i class="bi bi-search me-1"></i>Laporan Kehilangan
            </a>
        </li>
    </ul>

    <?php if ($active_tab == 'barang'): ?>
    <div class="card card-main">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3">Barang</th>
                            <th>Lokasi / Tanggal</th>
                            <th>Dilaporkan Oleh</th>
                            <th>Status</th>
                            <th class="text-center pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($query_barang)): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3 py-2">
                                    <img src="<?php echo !empty($row['foto']) ? 'uploads/'.htmlspecialchars($row['foto']) : 'https://via.placeholder.com/55?text=N/A'; ?>" class="img-thumb">
                                    <div>
                                        <div class="fw-bold small"><?php echo htmlspecialchars($row['nama_barang']); ?></div>
                                        <span class="badge bg-warning text-dark" style="font-size:0.65rem;"><?php echo $row['kategori']; ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="small fw-bold"><?php echo htmlspecialchars($row['lokasi']); ?></div>
                                <div class="text-muted small"><?php echo date('d M Y', strtotime($row['tgl_lapor'])); ?></div>
                            </td>
                            <td class="small text-muted"><?php echo htmlspecialchars($row['dilaporkan_oleh']); ?></td>
                            <td>
                                <?php
                                $st = strtolower($row['status']);
                                $st_css = $st == 'tersedia' ? 'status-tersedia' : 'status-diambil';
                                ?>
                                <span class="badge-status <?php echo $st_css; ?>">
                                    <?php echo strtoupper($row['status']); ?>
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <form method="POST" class="d-inline-flex gap-1 align-items-center">
                                    <input type="hidden" name="id_barang" value="<?php echo $row['id_barang']; ?>">
                                    <select name="status" class="form-select form-select-sm" style="width:120px;">
                                        <option value="Tersedia" <?php echo $row['status']=='Tersedia'?'selected':''; ?>>Tersedia</option>
                                        <option value="Diambil"  <?php echo $row['status']=='Diambil'?'selected':''; ?>>Diambil</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-dark btn-sm btn-sm-action">Simpan</button>
                                </form>
                                <a href="?hapus_barang=<?php echo $row['id_barang']; ?>"
                                   class="btn btn-outline-danger btn-sm btn-sm-action ms-1"
                                   onclick="return confirm('Hapus data ini secara permanen?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php elseif ($active_tab == 'tambah'): ?>
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card card-main p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-plus-circle-fill text-danger me-2"></i>Tambah Data Barang Temuan</h5>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control" placeholder="Contoh: Dompet Kulit Hitam" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Elektronik">Elektronik</option>
                                <option value="Dokumen">Dokumen</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Pakaian">Pakaian</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold small">Lokasi Penemuan</label>
                            <input type="text" name="lokasi" class="form-control" placeholder="Nama Stasiun / Gerbong" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Foto Barang</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" name="tambah_laporan" class="btn btn-danger w-100 fw-bold rounded-pill">
                        <i class="bi bi-cloud-upload me-2"></i>Simpan Data Temuan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php elseif ($active_tab == 'klaim'): ?>
    <?php if (mysqli_num_rows($query_klaim) == 0): ?>
        <div class="card card-main p-5 text-center">
            <i class="bi bi-inbox display-4 text-muted mb-3"></i>
            <p class="text-muted">Belum ada pengajuan klaim.</p>
        </div>
    <?php else: ?>
        <?php while($row = mysqli_fetch_assoc($query_klaim)): ?>
        <div class="card card-main mb-4 p-4">
            <div class="row g-3 align-items-start">
                <div class="col-md-4">
                    <div class="d-flex gap-3 align-items-center">
                        <img src="<?php echo !empty($row['foto_barang']) ? 'uploads/'.htmlspecialchars($row['foto_barang']) : 'https://via.placeholder.com/55?text=N/A'; ?>" class="img-thumb">
                        <div>
                            <div class="fw-bold small"><?php echo htmlspecialchars($row['nama_barang']); ?></div>
                            <div class="text-muted small"><?php echo htmlspecialchars($row['lokasi']); ?></div>
                            <span class="badge bg-warning text-dark" style="font-size:0.65rem;"><?php echo $row['kategori']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="small"><b>Pengklaim:</b> <?php echo htmlspecialchars($row['username_klaim']); ?></div>
                    <div class="small"><b>Nama Pemilik:</b> <?php echo htmlspecialchars($row['nama_pemilik']); ?></div>
                    <div class="small"><b>No. Identitas:</b> <?php echo htmlspecialchars($row['no_identitas']); ?></div>
                    <div class="small mt-1"><b>Keterangan:</b><br><span class="text-muted"><?php echo nl2br(htmlspecialchars(substr($row['keterangan'],0,150))); ?></span></div>
                    <?php if (!empty($row['bukti_foto'])): ?>
                    <a href="uploads/<?php echo htmlspecialchars($row['bukti_foto']); ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill mt-2" style="font-size:0.75rem;">
                        <i class="bi bi-image me-1"></i>Lihat Bukti Identitas
                    </a>
                    <?php endif; ?>
                    <div class="small text-muted mt-1">Diajukan: <?php echo date('d M Y H:i', strtotime($row['tgl_klaim'])); ?></div>
                </div>
                <div class="col-md-3">
                    <div class="mb-2">
                        <span class="badge-status status-<?php echo strtolower($row['status_klaim']); ?>">
                            <?php echo $row['status_klaim']; ?>
                        </span>
                    </div>
                    <?php if ($row['status_klaim'] == 'Menunggu' || $row['status_klaim'] == 'Diverifikasi'): ?>
                    <button class="btn btn-sm btn-outline-primary w-100 mb-1 rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalKlaim<?php echo $row['id_klaim']; ?>">
                        <i class="bi bi-gear me-1"></i>Proses Klaim
                    </button>
                    <?php elseif ($row['status_klaim'] == 'Diserahkan'): ?>
                    <div class="alert alert-success py-1 px-2 small mb-0 rounded-3 mt-2">
                        <i class="bi bi-check2-circle me-1"></i>Sudah Diserahkan
                    </div>
                    <?php elseif ($row['status_klaim'] == 'Ditolak'): ?>
                    <div class="alert alert-danger py-1 px-2 small mb-0 rounded-3 mt-2">
                        <i class="bi bi-x-circle me-1"></i>Klaim Ditolak
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalKlaim<?php echo $row['id_klaim']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h6 class="modal-title fw-bold">Proses Klaim: <?php echo htmlspecialchars($row['nama_barang']); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="id_klaim" value="<?php echo $row['id_klaim']; ?>">
                        <input type="hidden" name="action" value="" id="action_<?php echo $row['id_klaim']; ?>">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Catatan Petugas</label>
                                <textarea name="catatan_petugas" class="form-control" rows="3" placeholder="Tulis catatan verifikasi, alasan penolakan, atau instruksi pengambilan..."></textarea>
                            </div>
                            <p class="small text-muted mb-0">Pilih tindakan untuk klaim ini:</p>
                        </div>
                        <div class="modal-footer gap-2">
                            <?php if ($row['status_klaim'] == 'Menunggu'): ?>
                            <button type="submit" name="verif_klaim" value="1"
                                onclick="document.getElementById('action_<?php echo $row['id_klaim']; ?>').value='verifikasi'"
                                class="btn btn-primary btn-sm rounded-pill px-3">
                                <i class="bi bi-shield-check me-1"></i>Verifikasi
                            </button>
                            <?php endif; ?>
                            <?php if ($row['status_klaim'] == 'Diverifikasi'): ?>
                            <button type="submit" name="verif_klaim" value="1"
                                onclick="document.getElementById('action_<?php echo $row['id_klaim']; ?>').value='serahkan'"
                                class="btn btn-success btn-sm rounded-pill px-3">
                                <i class="bi bi-box-seam me-1"></i>Serahkan Barang
                            </button>
                            <?php endif; ?>
                            <button type="submit" name="verif_klaim" value="1"
                                onclick="document.getElementById('action_<?php echo $row['id_klaim']; ?>').value='tolak'"
                                class="btn btn-danger btn-sm rounded-pill px-3">
                                <i class="bi bi-x-circle me-1"></i>Tolak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>

    <?php elseif ($active_tab == 'kehilangan'): ?>
    <?php if (mysqli_num_rows($query_kehilangan) == 0): ?>
        <div class="card card-main p-5 text-center">
            <i class="bi bi-inbox display-4 text-muted mb-3"></i>
            <p class="text-muted">Belum ada laporan kehilangan.</p>
        </div>
    <?php else: ?>
        <?php while($row = mysqli_fetch_assoc($query_kehilangan)): ?>
        <div class="card card-main mb-3 p-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-1 text-center">
                    <?php if(!empty($row['foto'])): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" class="img-thumb">
                    <?php else: ?>
                        <div class="img-thumb bg-light d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-question-circle text-muted fs-4"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-5">
                    <div class="fw-bold"><?php echo htmlspecialchars($row['nama_barang']); ?></div>
                    <small class="text-muted">
                        <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['username']); ?> &nbsp;|&nbsp;
                        <i class="bi bi-geo-alt me-1"></i><?php echo htmlspecialchars($row['lokasi']); ?> &nbsp;|&nbsp;
                        <i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($row['tgl_hilang'])); ?>
                    </small>
                    <p class="small text-muted mt-1 mb-0"><?php echo htmlspecialchars(substr($row['deskripsi'],0,120)); ?>...</p>
                </div>
                <div class="col-md-3 text-center">
                    <?php
                    $st  = $row['status'];
                    $cls = $st=='Ditemukan' ? 'status-ditemukan' : ($st=='Diproses' ? 'status-diproses' : 'status-mencari');
                    ?>
                    <span class="badge-status <?php echo $cls; ?>"><?php echo $st; ?></span>
                    <?php if (!empty($row['id_barang_cocok'])): ?>
                    <div class="small text-muted mt-1">ID Temuan: #<?php echo $row['id_barang_cocok']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-3 text-end">
                    <?php if ($row['status'] == 'Mencari'): ?>
                    <button class="btn btn-sm btn-outline-primary rounded-pill"
                        data-bs-toggle="modal" data-bs-target="#modalCocok<?php echo $row['id_laporan']; ?>">
                        <i class="bi bi-diagram-3 me-1"></i>Cocokkan Barang
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalCocok<?php echo $row['id_laporan']; ?>" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-primary text-white">
                        <h6 class="modal-title fw-bold">Cocokkan: <?php echo htmlspecialchars($row['nama_barang']); ?></h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="id_laporan" value="<?php echo $row['id_laporan']; ?>">
                            <p class="small text-muted mb-3">Pilih barang temuan yang sesuai dengan laporan kehilangan ini:</p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small">Pilih Barang Temuan (Tersedia)</label>
                                <select name="id_barang_cocok" class="form-select" required>
                                    <option value="">-- Pilih Barang Temuan --</option>
                                    <?php
                                    mysqli_data_seek($query_barang_aktif, 0);
                                    while($b = mysqli_fetch_assoc($query_barang_aktif)): ?>
                                    <option value="<?php echo $b['id_barang']; ?>">
                                        #<?php echo $b['id_barang']; ?> — <?php echo htmlspecialchars($b['nama_barang']); ?> (<?php echo htmlspecialchars($b['lokasi']); ?>)
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="cocokkan" class="btn btn-primary rounded-pill px-4">
                                <i class="bi bi-check2-circle me-1"></i>Simpan Pencocokan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    <?php endif; ?>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>