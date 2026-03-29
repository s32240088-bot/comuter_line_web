<?php
include "connect.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['username'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = mysqli_real_escape_string($conn, $_POST['kategori']);
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $user_pelapor = $_SESSION['username'];
    $tgl_lapor   = date('Y-m-d');

    $foto_baru = "";
    if (!empty($_FILES['foto']['name'])) {
        $ekstensi_diperoleh = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $ekstensi_boleh = array('jpg', 'jpeg', 'png');

        if (in_array($ekstensi_diperoleh, $ekstensi_boleh)) {
            $foto_baru = "IMG_" . time() . "_" . uniqid() . "." . $ekstensi_diperoleh;
            if (!is_dir("uploads/")) mkdir("uploads/", 0777, true);
            move_uploaded_file($_FILES['foto']['tmp_name'], "uploads/" . $foto_baru);
        } else {
            echo "<script>alert('Format gambar harus JPG/PNG'); window.history.back();</script>";
            exit();
        }
    }

    $query = "INSERT INTO barang_temuan (nama_barang, kategori, lokasi, deskripsi, tgl_lapor, foto, dilaporkan_oleh, status) 
              VALUES ('$nama_barang', '$kategori', '$lokasi', '$deskripsi', '$tgl_lapor', '$foto_baru', '$user_pelapor', 'Tersedia')";

    $simpan = mysqli_query($conn, $query);
    if ($simpan) {
        echo "<script>alert('Laporan Berhasil! Barang temuan Anda sudah terdaftar.'); window.location='home_user.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
} else {
    header("Location: home_user.php");
}
?>