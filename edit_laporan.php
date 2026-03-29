<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username  = $_SESSION['username'];
$id_barang = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_barang) {
    header("Location: laporan_user.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM barang_temuan WHERE id_barang = ? AND dilaporkan_oleh = ?");
$stmt->bind_param("is", $id_barang, $username);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='laporan_user.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);

    $foto_final = $data['foto'];

    if (!empty($_FILES['foto']['name'])) {
        $nama_file  = $_FILES['foto']['name'];
        $tmp_file   = $_FILES['foto']['tmp_name'];
        $ekstensi   = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        $ekstensi_boleh = ['jpg', 'jpeg', 'png'];

        if (in_array($ekstensi, $ekstensi_boleh)) {
            if (!empty($data['foto']) && file_exists("uploads/" . $data['foto'])) {
                unlink("uploads/" . $data['foto']);
            }
            $foto_final = "IMG_" . time() . "_" . uniqid() . "." . $ekstensi;
            if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);
            move_uploaded_file($tmp_file, "uploads/" . $foto_final);
        }
    }

    $update = mysqli_query($conn, "UPDATE barang_temuan SET
                nama_barang = '$nama_barang',
                kategori    = '$kategori',
                lokasi      = '$lokasi',
                deskripsi   = '$deskripsi',
                foto        = '$foto_final'
                WHERE id_barang = '$id_barang' AND dilaporkan_oleh = '$username'");

    if ($update) {
        echo "<script>alert('Laporan berhasil diperbarui!'); window.location='laporan_user.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui laporan.');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan | Commuter L&F</title>
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
        .card-custom { border: none; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.2); width: 100%; max-width: 520px; }
        .form-control, .form-select { border-radius: 10px; padding: 10px 15px; border: 1px solid #e0e0e0; }
        .form-control:focus { border-color: var(--kci-red); box-shadow: 0 0 0 0.2rem rgba(227,30,36,0.1); }
        .preview-img-container { width: 120px; height: 120px; border-radius: 15px; overflow: hidden; border: 3px solid #eee; margin: 0 auto; }
        .preview-img-container img { width: 100%; height: 100%; object-fit: cover; }
        .btn-update { background-color: var(--kci-red); color: white; border: none; font-weight: 600; transition: 0.3s; }
        .btn-update:hover { background-color: #b3171b; transform: translateY(-2px); color: white; }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="laporan_user.php" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Laporan Saya
        </a>
        <span class="text-white small fw-semibold"><i class="bi bi-pencil-square me-1 text-warning"></i>Edit Laporan</span>
    </div>
</div>

<div class="content-wrapper">
    <div class="card-custom p-4 bg-white">
        <div class="text-center mb-4">
            <div class="fw-bold text-danger" style="font-size:1.1rem;"><i class="bi bi-train-front-fill me-2"></i>COMMUTER <span class="text-warning">L&F</span></div>
            <h5 class="fw-bold mt-2 mb-1">Edit Laporan Barang</h5>
            <p class="text-muted small">Perbarui detail barang temuan Anda</p>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="text-center mb-4">
                <div class="preview-img-container mb-2">
                    <img src="uploads/<?php echo htmlspecialchars($data['foto']); ?>" id="img-preview" onerror="this.src='https://via.placeholder.com/120?text=No+Image'">
                </div>
                <label for="fotoInput" class="btn btn-sm btn-outline-secondary rounded-pill px-3 mt-1">
                    <i class="bi bi-camera me-1"></i> Ganti Foto
                </label>
                <input type="file" name="foto" id="fotoInput" class="d-none" accept="image/*">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small text-muted">NAMA BARANG</label>
                <input type="text" name="nama_barang" class="form-control" value="<?php echo htmlspecialchars($data['nama_barang']); ?>" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small text-muted">KATEGORI</label>
                    <select name="kategori" class="form-select" required>
                        <option value="Elektronik" <?= $data['kategori'] == 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
                        <option value="Dokumen"    <?= $data['kategori'] == 'Dokumen'    ? 'selected' : '' ?>>Dokumen</option>
                        <option value="Aksesoris"  <?= $data['kategori'] == 'Aksesoris'  ? 'selected' : '' ?>>Aksesoris</option>
                        <option value="Lainnya"    <?= $data['kategori'] == 'Lainnya'    ? 'selected' : '' ?>>Lainnya</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold small text-muted">LOKASI STASIUN</label>
                    <input type="text" name="lokasi" class="form-control" value="<?php echo htmlspecialchars($data['lokasi']); ?>" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small text-muted">DESKRIPSI</label>
                <textarea name="deskripsi" class="form-control" rows="3" required><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
            </div>

            <div class="d-flex gap-2">
                <a href="laporan_user.php" class="btn btn-outline-secondary w-50 rounded-pill">Batal</a>
                <button type="submit" class="btn btn-update w-50 rounded-pill shadow">
                    <i class="bi bi-floppy me-1"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('fotoInput').onchange = function (evt) {
    const [file] = this.files;
    if (file) {
        document.getElementById('img-preview').src = URL.createObjectURL(file);
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>