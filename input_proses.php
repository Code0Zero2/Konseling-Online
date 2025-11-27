<?php
session_start();
include 'connection.php';

if(!isset($_SESSION['user_id'])){
    header('Location: signin.php');
    exit;
}

$dokter_id = $_SESSION['user_id'];
$judul = $_POST['judul'];
$isi = $_POST['isi'];
$tanggal = date('Y-m-d H:i:s');

$query = mysqli_query($conn, "INSERT INTO artikel (dokter_id, judul, isi, tanggal) VALUE ('$dokter_id', '$judul', '$isi', '$tanggal')");

if ($query) {
    header("Location: dashboard_dokter.php");
    exit;
} else {
    echo "Gagal menambahkan data: " . mysqli_error($connect);
    echo '<br><a href="tambah_artikel.php">Kembali ke form input</a>';
}

?>