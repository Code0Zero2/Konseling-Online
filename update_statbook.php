<?php
session_start();
include 'connection.php';

// Hanya dokter yang boleh
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'dokter') {
    header("Location: signin.php");
    exit;
}

$booking_id = $_GET['id'];

// Ambil status sekarang
$q = mysqli_query($conn, "SELECT * FROM booking WHERE booking_id='$booking_id'");
$data = mysqli_fetch_assoc($q);

// Jika tidak ditemukan
if (!$data) {
    die("Booking tidak ditemukan");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    mysqli_query($conn, "
        UPDATE booking 
        SET status='$status' 
        WHERE booking_id='$booking_id'
    ");

    header("Location: crud_jadwal.php?update=success");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
<title>Ubah Status Booking</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body class="p-4">

<h3>Ubah Status Booking</h3>

<form method="POST">

<label>Status Baru:</label>
<select name="status" class="form-control" required>
    <option value="menunggu">Menunggu</option>
    <option value="disetujui">Disetujui</option>
    <option value="selesai">Selesai</option>
</select>

<br>

<button class="btn btn-primary">Simpan</button>
<a class="btn btn-secondary" href="crud_jadwal.php">Batal</a>

</form>

</body>
</html>
