<?php
include "connect.php";
session_start();

if (!isset($_SESSION['username']) || !isset($_GET['id'])) {
    header("Location: laporan_user.php");
    exit();
}

$id_barang = $_GET['id'];
$username = $_SESSION['username'];


$cek_pemilik = mysqli_query($conn, "SELECT foto FROM barang_temuan WHERE id_barang = '$id_barang' AND dilaporkan_oleh = '$username'");
$data = mysqli_fetch_assoc($cek_pemilik);

if ($data) {
    if (!empty($data['foto']) && file_exists("uploads/" . $data['foto'])) {
        unlink("uploads/" . $data['foto']);
    }

    $delete = mysqli_query($conn, "DELETE FROM barang_temuan WHERE id_barang = '$id_barang'");

    if ($delete) {
        echo "<script>alert('Laporan berhasil dihapus'); window.location='laporan_user.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus laporan'); window.location='laporan_user.php';</script>";
    }
} else {
    echo "<script>alert('Akses ditolak!'); window.location='laporan_user.php';</script>";
}
?>