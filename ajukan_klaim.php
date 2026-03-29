<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_barang) {
    header("Location: home_user.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM barang_temuan WHERE id_barang = ? AND status = 'Tersedia'");
$stmt->bind_param("i", $id_barang);
$stmt->execute();
$barang = $stmt->get_result()->fetch_assoc();

if (!$barang) {
    echo "<script>alert('Barang tidak ditemukan atau sudah diklaim!'); window.location='home_user.php';</script>";
    exit();
}

$cek = $conn->prepare("SELECT id_klaim FROM klaim_barang WHERE id_barang = ? AND username_klaim = ? AND status_klaim NOT IN ('Ditolak')");
$cek->bind_param("is", $id_barang, $username);
$cek->execute();
$sudah_klaim = $cek->get_result()->num_rows > 0;

$my_laporan = mysqli_query($conn, "SELECT * FROM laporan_kehilangan WHERE username='$username' AND status != 'Ditemukan' ORDER BY tgl_lapor DESC");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_klaim']) && !$sudah_klaim) {
    $nama_pemilik  = mysqli_real_escape_string($conn, $_POST['nama_pemilik']);
    $no_identitas  = mysqli_real_escape_string($conn, $_POST['no_identitas']);
    $keterangan    = mysqli_real_escape_string($conn, $_POST['keterangan']);
    $id_laporan_ref = !empty($_POST['id_laporan_ref']) ? (int)$_POST['id_laporan_ref'] : null;
    $bukti_foto    = "";

    if (!empty($_FILES['bukti_foto']['name'])) {
        $ext = strtolower(pathinfo($_FILES['bukti_foto']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg','jpeg','png'])) {
            $bukti_foto = "BUKTI_" . time() . "_" . uniqid() . "." . $ext;
            if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);
            move_uploaded_file($_FILES['bukti_foto']['tmp_name'], "uploads/" . $bukti_foto);
        }
    }

    $id_lap_val = $id_laporan_ref ? $id_laporan_ref : null;
    $stmt_ins = $conn->prepare("INSERT INTO klaim_barang (id_barang, id_laporan, username_klaim, nama_pemilik, no_identitas, keterangan, bukti_foto) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt_ins->bind_param("iisssss", $id_barang, $id_lap_val, $username, $nama_pemilik, $no_identitas, $keterangan, $bukti_foto);

    if ($stmt_ins->execute()) {
        echo "<script>alert('Klaim berhasil diajukan! Petugas akan memverifikasi identitas Anda.'); window.location='status_klaim.php';</script>";
        exit();
    } else {
        echo "<script>alert('Gagal mengajukan klaim. Silakan coba lagi.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Klaim | Commuter L&F</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --kci-red: #e31e24; }
        body { font-family: 'Poppins', sans-serif; background: linear-gradient(135deg, #e31e24 0%, #8b1216 100%); min-height: 100vh; padding: 0; }
        .top-bar { background: rgba(0,0,0,0.2); border-bottom: 1px solid rgba(255,255,255,0.15); padding: 12px 0; }
        .btn-back { background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 50px; padding: 6px 18px; font-size: 0.85rem; transition: 0.2s; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back:hover { background: rgba(255,255,255,0.3); color: white; }
        .content-wrapper { padding: 30px 0 50px; }
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); }
        .form-control, .form-select { border-radius: 10px; padding: 10px 14px; }
        .form-control:focus, .form-select:focus { border-color: #e31e24; box-shadow: 0 0 0 0.2rem rgba(227,30,36,0.15); }
        .btn-klaim { background: #e31e24; color: white; border-radius: 10px; padding: 12px; font-weight: 700; border: none; }
        .btn-klaim:hover { background: #b3171b; color: white; }
        .item-preview { border-radius: 15px; overflow: hidden; }
        .item-img { width: 100%; height: 200px; object-fit: cover; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="detail_barang.php?id=<?php echo $id_barang; ?>" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Detail
        </a>
        <span class="text-white small fw-semibold"><i class="bi bi-shield-check me-1 text-warning"></i>Pengajuan Klaim</span>
    </div>
</div>

<div class="content-wrapper">
<div class="container">
    <div class="row g-4 justify-content-center">
        <!-- Info Barang -->
        <div class="col-md-4">
            <div class="card card-custom item-preview">
                <img src="<?php echo !empty($barang['foto']) ? 'uploads/'.$barang['foto'] : 'https://via.placeholder.com/400x200?text=No+Image'; ?>" class="item-img">
                <div class="p-4">
                    <span class="badge bg-warning text-dark mb-2"><?php echo $barang['kategori']; ?></span>
                    <h5 class="fw-bold"><?php echo htmlspecialchars($barang['nama_barang']); ?></h5>
                    <p class="text-muted small"><i class="bi bi-geo-alt-fill text-danger me-1"></i><?php echo htmlspecialchars($barang['lokasi']); ?></p>
                    <p class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?php echo date('d M Y', strtotime($barang['tgl_lapor'])); ?></p>
                    <hr>
                    <p class="small"><?php echo nl2br(htmlspecialchars($barang['deskripsi'])); ?></p>
                    <div class="alert alert-info small py-2 px-3 rounded-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Setelah klaim diverifikasi, Anda akan dihubungi untuk pengambilan barang.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-custom p-4">
                <h5 class="fw-bold mb-1"><i class="bi bi-shield-check text-danger me-2"></i>Formulir Pengajuan Klaim</h5>
                <p class="text-muted small mb-4">Isi data dengan benar. Identitas Anda akan diverifikasi oleh petugas.</p>

                <?php if ($sudah_klaim): ?>
                <div class="alert alert-warning rounded-3">
                    <i class="bi bi-clock-history me-2"></i>
                    Anda sudah mengajukan klaim untuk barang ini. Silakan cek status di halaman <a href="status_klaim.php" class="fw-bold">Status Klaim</a>.
                </div>
                <?php else: ?>
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama Lengkap Pemilik</label>
                        <input type="text" name="nama_pemilik" class="form-control" placeholder="Sesuai KTP" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nomor Identitas (KTP/SIM/Passport)</label>
                        <input type="text" name="no_identitas" class="form-control" placeholder="16 digit NIK atau nomor SIM" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Keterangan / Bukti Kepemilikan</label>
                        <textarea name="keterangan" class="form-control" rows="4"
                            placeholder="Jelaskan ciri-ciri spesifik barang, isi dompet, file dalam HP, dll. yang hanya pemilik asli yang tahu." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Foto Bukti Identitas <span class="text-muted">(KTP/SIM - opsional tapi disarankan)</span></label>
                        <input type="file" name="bukti_foto" class="form-control" accept="image/*">
                    </div>
                    <?php if (mysqli_num_rows($my_laporan) > 0): ?>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Hubungkan dengan Laporan Kehilangan Saya <span class="text-muted">(opsional)</span></label>
                        <select name="id_laporan_ref" class="form-select">
                            <option value="">-- Pilih laporan terkait (jika ada) --</option>
                            <?php
                            mysqli_data_seek($my_laporan, 0);
                            while($lap = mysqli_fetch_assoc($my_laporan)): ?>
                            <option value="<?php echo $lap['id_laporan']; ?>">
                                <?php echo htmlspecialchars($lap['nama_barang']); ?> — <?php echo date('d M Y', strtotime($lap['tgl_hilang'])); ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <?php endif; ?>
                    <button type="submit" name="submit_klaim" class="btn btn-klaim w-100">
                        <i class="bi bi-send-check-fill me-2"></i>Ajukan Klaim Sekarang
                    </button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>