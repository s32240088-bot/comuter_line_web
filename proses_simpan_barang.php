<?php
include "connect.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['username'])) {
    $nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $kategori    = $_POST['kategori'];
    $lokasi      = mysqli_real_escape_string($conn, $_POST['lokasi']);
    $user        = $_SESSION['username'];

    $target_dir = "uploads/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $foto = $_FILES['foto']['name'];
    $tmp  = $_FILES['foto']['tmp_name'];
    $path = $target_dir . basename($foto);

    if (!empty($foto)) {
        if (move_uploaded_file($tmp, $path)) {
            $sql = "INSERT INTO barang_temuan (nama_barang, kategori, lokasi, foto, dilaporkan_oleh) 
                    VALUES ('$nama_barang', '$kategori', '$lokasi', '$foto', '$user')";
            
            if (mysqli_query($conn, $sql)) {
                echo "<script>alert('Laporan berhasil dikirim!'); window.location.href='home_user.php';</script>";
            } else {
                echo "Error Database: " . mysqli_error($conn);
            }
        } else {
            echo "<script>alert('Gagal mengunggah gambar. Periksa izin folder uploads!'); window.location.href='home_user.php';</script>";
        }
    } else {
        echo "<script>alert('Harap pilih foto barang!'); window.location.href='home_user.php';</script>";
    }
}
?>