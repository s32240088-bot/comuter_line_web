<?php
include "connect.php";
session_start();
$is_logged_in = isset($_SESSION['username']);

$kategori_filter = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM barang_temuan WHERE 1=1";
$params = [];
$types = "";

if ($kategori_filter) {
    $sql .= " AND kategori = ?";
    $params[] = $kategori_filter;
    $types .= "s";
}

if ($search_query) {
    $sql .= " AND (nama_barang LIKE ? OR deskripsi LIKE ? OR lokasi LIKE ?)";
    $search_param = "%" . $search_query . "%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $types .= "sss";
}

$sql .= " AND status = 'Tersedia' ORDER BY tgl_lapor DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$query_barang = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found - Commuter Line</title>

    <link rel="icon" type="image/png" href="uploads/logo_kereta.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --kci-red: #e31e24; --kci-yellow: #ffc107; }
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; color: #333; min-height: 100vh; display: flex; flex-direction: column; overflow-x: hidden; }
        
        /* Navbar */
        .navbar-kci { background-color: var(--kci-red); border-bottom: 5px solid var(--kci-yellow); padding: 0.8rem 0; }
        .nav-link:hover { color: var(--kci-yellow) !important; }
        .profile-img { width: 35px; height: 35px; object-fit: cover; border-radius: 50%; border: 2px solid #fff; }

        /* Hero Section */
        .hero-section { 
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('uploads/background_user.png') no-repeat center center; 
            background-size: cover; 
            background-attachment: fixed;
            color: white; 
            padding: 100px 0; 
            text-align: center; 
            border-bottom-left-radius: 50px; 
            border-bottom-right-radius: 50px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* Kartu Barang */
        .card-lost { border-radius: 15px; transition: 0.3s; overflow: hidden; border: none; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .card-lost:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important; }
        .card-img-top { height: 200px; object-fit: cover; }
        
        /* Kategori Scrollable */
        .category-wrapper { 
            display: flex; 
            gap: 10px; 
            overflow-x: auto; 
            padding: 15px 5px; 
            scrollbar-width: none;
            justify-content: flex-start;
        }
        @media (min-width: 992px) { .category-wrapper { justify-content: center; } }
        .category-wrapper::-webkit-scrollbar { display: none; }
        
        .category-card { 
            min-width: fit-content; 
            background: white; 
            border: 2px solid var(--kci-red); 
            border-radius: 30px; 
            padding: 8px 25px; 
            text-align: center; 
            transition: all 0.3s ease; 
            text-decoration: none; 
            color: var(--kci-red); 
            font-weight: 600; 
            white-space: nowrap; 
        }
        .category-card:hover, .category-card.active { background: var(--kci-red); color: white; transform: translateY(-3px); }
        
        /* Tombol Cepat */
        .quick-action { 
            border-radius: 15px; 
            border: none; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.08); 
            transition: 0.3s; 
            text-decoration: none; 
            display: block; 
            padding: 25px; 
            text-align: center; 
            background: white; 
            color: #333; 
            height: 100%;
        }
        .quick-action:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); color: var(--kci-red); }

        @media (max-width: 576px) {
            .hero-section { padding: 60px 0; border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; background-attachment: scroll; }
            .display-4 { font-size: 1.8rem; }
            .quick-action { padding: 15px 10px; }
            .quick-action div { font-size: 0.8rem; }
            .quick-action i { font-size: 1.5rem !important; }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-kci sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="home_user.php">
            <i class="bi bi-train-front-fill me-2 text-warning"></i>
            <span>COMMUTER <span class="text-warning">LOST&FOUND</span></span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item"><a class="nav-link px-3" href="#" data-bs-toggle="modal" data-bs-target="#modalLaporTemuan">Laporkan Temuan</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="laporan_kehilangan.php">Laporan Hilang</a></li>
                    <li class="nav-item dropdown ms-lg-3">
                        <a class="nav-link dropdown-toggle d-flex align-items-center bg-white bg-opacity-10 rounded-pill ps-2 pe-3" href="#" role="button" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['username']); ?>&background=ffc107&color=333" class="profile-img me-2">
                            <span class="fw-semibold small text-white"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2">
                            <li><a class="dropdown-item" href="edit_profile.php"><i class="bi bi-person-gear me-2"></i>Edit Profil</a></li>
                            <li><a class="dropdown-item" href="laporan_user.php"><i class="bi bi-archive me-2"></i>Riwayat Laporan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-3"><a class="btn btn-warning fw-bold px-4 rounded-pill shadow-sm" href="login.php">LOGIN</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<header class="hero-section">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3"><?php echo $is_logged_in ? "Halo, " . htmlspecialchars($_SESSION['username']) . "!" : "Portal Lost & Found"; ?></h1>
        <p class="lead mb-4 opacity-75 px-3">Membantu mengembalikan barang berharga Anda yang tertinggal di Commuter Line.</p>
        <?php if (!$is_logged_in): ?>
            <a href="login.php" class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow">Mulai Sekarang</a>
        <?php endif; ?>
    </div>
</header>

<main class="container my-5 px-3">
    <?php if ($is_logged_in): ?>
    <div class="row g-3 g-md-4 mb-5">
        <div class="col-6 col-md-3">
            <a href="#" data-bs-toggle="modal" data-bs-target="#modalLaporTemuan" class="quick-action">
                <i class="bi bi-megaphone-fill fs-2 text-danger mb-2 d-block"></i>
                <div class="fw-bold">Laporkan Temuan</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="laporan_kehilangan.php" class="quick-action">
                <i class="bi bi-search-heart-fill fs-2 text-warning mb-2 d-block"></i>
                <div class="fw-bold">Cari Barang Hilang</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="status_klaim.php" class="quick-action">
                <i class="bi bi-clipboard2-check-fill fs-2 text-success mb-2 d-block"></i>
                <div class="fw-bold">Status Klaim Saya</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="laporan_user.php" class="quick-action">
                <i class="bi bi-archive-fill fs-2 text-primary mb-2 d-block"></i>
                <div class="fw-bold">Riwayat Laporan</div>
            </a>
        </div>
    </div>

    <div class="mb-5">
        <form action="home_user.php" method="GET" class="mb-4">
            <div class="input-group shadow-sm rounded-pill overflow-hidden">
                <input type="text" name="search" class="form-control border-0 ps-4" placeholder="Cari barang atau stasiun..." value="<?php echo htmlspecialchars($search_query); ?>">
                <button class="btn btn-danger px-4" type="submit"><i class="bi bi-search me-2"></i>Cari</button>
            </div>
        </form>
        
        <div class="category-wrapper">
            <a href="home_user.php" class="category-card <?php echo $kategori_filter==''?'active':''; ?>">Semua</a>
            <a href="home_user.php?kategori=Elektronik" class="category-card <?php echo $kategori_filter=='Elektronik'?'active':''; ?>">Elektronik</a>
            <a href="home_user.php?kategori=Dokumen" class="category-card <?php echo $kategori_filter=='Dokumen'?'active':''; ?>">Dokumen</a>
            <a href="home_user.php?kategori=Aksesoris" class="category-card <?php echo $kategori_filter=='Aksesoris'?'active':''; ?>">Aksesoris</a>
            <a href="home_user.php?kategori=Pakaian" class="category-card <?php echo $kategori_filter=='Pakaian'?'active':''; ?>">Pakaian</a>
            <a href="home_user.php?kategori=Lainnya" class="category-card <?php echo $kategori_filter=='Lainnya'?'active':''; ?>">Lainnya</a>
        </div>
    </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold m-0">Barang Temuan Terbaru</h3>
        <span class="badge bg-danger rounded-pill px-3 py-2">Update Hari Ini</span>
    </div>

    <div class="row g-4">
        <?php if (mysqli_num_rows($query_barang) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query_barang)): ?>
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 card-lost">
                    <div class="position-relative">
                        <img src="<?php echo !empty($row['foto']) ? 'uploads/'.htmlspecialchars($row['foto']) : 'https://via.placeholder.com/300x200?text=No+Image'; ?>" class="card-img-top" alt="Barang">
                        <span class="badge bg-warning text-dark position-absolute top-0 end-0 m-3"><?php echo $row['kategori']; ?></span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-bold text-truncate"><?php echo htmlspecialchars($row['nama_barang']); ?></h6>
                        <p class="card-text small text-muted mb-2"><i class="bi bi-geo-alt-fill text-danger"></i> <?php echo htmlspecialchars($row['lokasi']); ?></p>
                        <p class="card-text mb-3 small opacity-75"><i class="bi bi-calendar3"></i> <?php echo date('d M Y', strtotime($row['tgl_lapor'])); ?></p>
                        
                        <?php if ($is_logged_in): ?>
                            <a href="detail_barang.php?id=<?php echo $row['id_barang']; ?>" class="btn btn-outline-danger btn-sm w-100 rounded-pill fw-bold mt-auto">Detail & Klaim</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-secondary btn-sm w-100 rounded-pill fw-bold mt-auto">Login untuk detail</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-box-seam fs-1 text-muted"></i>
                <p class="text-muted mt-3">Belum ada barang temuan yang tersedia saat ini.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="bg-dark text-white pt-5 pb-4 mt-auto border-top border-5 border-warning">
    <div class="container text-center text-md-start">
        <div class="row">
            <div class="col-md-6 mb-4">
                <h5 class="fw-bold text-warning">COMMUTER <span class="text-white">L&F</span></h5>
                <p class="small opacity-75">Layanan resmi pencarian barang temuan KAI Commuter Nusantara.</p>
            </div>
            <div class="col-md-6 mb-4 text-md-end">
                <h6 class="fw-bold text-warning mb-3">Kontak Layanan</h6>
                <p class="small mb-1"><i class="bi bi-envelope-at me-2"></i> cs@commuterline.id</p>
                <p class="small"><i class="bi bi-telephone me-2"></i> 1500-121</p>
                <p class="small opacity-50 mt-3">&copy; 2026 KAI Commuter Nusantara. All Rights Reserved.</p>
            </div>
        </div>
    </div>
</footer>

<?php if ($is_logged_in): ?>
<div class="modal fade" id="modalLaporTemuan" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-megaphone me-2"></i>Laporkan Barang Temuan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="proses_lapor.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Nama Barang</label>
                        <input type="text" name="nama_barang" class="form-control rounded-3" placeholder="Tas Ransel Hitam" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label class="form-label fw-bold small">Kategori</label>
                            <select name="kategori" class="form-select rounded-3">
                                <option value="Elektronik">Elektronik</option>
                                <option value="Dokumen">Dokumen</option>
                                <option value="Aksesoris">Aksesoris</option>
                                <option value="Pakaian">Pakaian</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col">
                            <label class="form-label fw-bold small">Lokasi Temuan</label>
                            <input type="text" name="lokasi" class="form-control rounded-3" placeholder="Nama Stasiun" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">Deskripsi Singkat</label>
                        <textarea name="deskripsi" class="form-control rounded-3" rows="3" placeholder="Ciri-ciri barang..." required></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-bold small">Unggah Foto</label>
                        <input type="file" name="foto" class="form-control rounded-3" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>