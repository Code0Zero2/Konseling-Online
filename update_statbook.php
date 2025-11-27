<?php
session_start();
include 'connection.php';

// Hanya dokter yang boleh
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header("Location: signin.php");
    exit;
}
$dokter_id = $_SESSION['user_id'];

// // $booking_id = $_GET['booking_id'];

// // Ambil status sekarang
// $query = mysqli_query($conn, "SELECT * FROM booking WHERE booking_id='$booking_id'");
// $data = mysqli_fetch_assoc($query);

// // Jika tidak ditemukan
// if (!$data) {
//     die("Booking tidak ditemukan");
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    $update = mysqli_query($conn, "
        UPDATE booking 
        SET status='$status' 
        WHERE booking_id='$booking_id'
    ");

    if ($update) {
        header("location:dashboard_dokter.php?success=update");
    } else {
        header("location:dashboard_dokter.php?error=update_gagal");
    }
    exit;
}

?>