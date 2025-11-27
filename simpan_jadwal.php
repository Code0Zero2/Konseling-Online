<?php
session_start();
include 'connection.php';

// Hanya dokter yang boleh
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header("Location: signin.php");
    exit;
}
$dokter_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jadwal_id = $_POST['jadwal_id'];
    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $cek = mysqli_query(
        $conn,
        "SELECT * FROM jadwal_dokter
        WHERE dokter_id = '$dokter_id'
        AND tanggal = '$tanggal'
        AND jam = '$jam'
        " . ($jadwal_id ? "AND jadwal_id != '$jadwal_id'" : "") . "
        "
    );

    if (mysqli_num_rows($cek) > 0) {
        header("Location: dashboard_dokter.php?error=duplikat");
        exit;
    }

    // update jadwal
    if (!empty($jadwal_id)) {
        $update = mysqli_query(
            $conn,
            "UPDATE jadwal_dokter SET
            tanggal = '$tanggal',
            jam = '$jam'
            WHERE jadwal_id = '$jadwal_id'
            "
        );
        if ($update) {
            header("location:dashboard_dokter.php?success=edit");
        } else {
            header("location:dashboard_dokter.php?error=update_gagal");
        }
        exit;
    }

    // tambah jadwal
    $tambah = mysqli_query(
        $conn,
        "INSERT INTO jadwal_dokter 
        (dokter_id, tanggal, jam, status) 
        VALUES ('$dokter_id', '$tanggal', '$jam', 'tersedia')
        "
    );
    if ($insert) {
        header("location:dashboard_dokter.php?success=tambah");
    } else {
        header("location:dashboard_dokter.php?error=tambah_gagal");
    }
    exit;
}
