<?php
session_start();
include 'connection.php';

// Hanya dokter yang boleh
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header("Location: signin.php");
    exit;
}
$dokter_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $jadwal_id = $_GET['id'];
    // pengecekan
    $cek = mysqli_query($conn, "
        SELECT * FROM jadwal_dokter
        WHERE jadwal_id = '$jadwal_id'
        AND dokter_id = '$dokter_id'
    ");

    if (mysqli_num_rows($cek) == 0) {
        header("Location: dashboard_dokter.php?error=unauthorized");
        exit;
    }

    $hapus = mysqli_query(
        $conn,
        "DELETE FROM jadwal_dokter 
        WHERE jadwal_id = '$jadwal_id'"
    );
    if ($hapus) {
        header("location:dashboard_dokter.php?success=hapus");
    } else {
        header("location:dashboard_dokter.php?error=hapus_gagal");
    }
    exit;
}
