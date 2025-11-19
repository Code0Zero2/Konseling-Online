<?php
include 'connection.php';
session_start();

// Contoh pasien login (ganti dengan session login aslimu)
// $pasien_id = 5;
$pasien_id = $_SESSION['id_pasien'];

// Pastikan ada jadwal_id
if (!isset($_GET['jadwal_id'])) {
    header("Location: daftar_jadwal.php");
    exit;
}

$jadwal_id = intval($_GET['jadwal_id']);

// Ambil data jadwal
$jadwal = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT j.*, u.nama AS nama_dokter
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    WHERE j.jadwal_id = '$jadwal_id'
"));

if (!$jadwal) {
    echo "<script>alert('Jadwal tidak ditemukan'); window.location='daftar_jadwal.php';</script>";
    exit;
}

// Cek apakah jadwal penuh
// if ($jadwal['status'] == "penuh") {
//     echo "<script>alert('Jadwal sudah penuh!'); window.location='daftar_jadwal.php';</script>";
//     exit;
// }

// Cek apakah jadwal sudah ada yang booking
$cek_slot = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM booking 
    WHERE jadwal_id='$jadwal_id' AND status IN ('menunggu','disetujui')
"));

if ($cek_slot['total'] >= 1) {
    echo "<script>alert('Jadwal sudah penuh!'); window.location='daftar_jadwal.php';</script>";
    exit;
}


// Cek apakah pasien sudah pernah booking jadwal yg sama
$cek_booking = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM booking 
    WHERE pasien_id='$pasien_id' AND jadwal_id='$jadwal_id'
"));

if ($cek_booking) {
    echo "<script>alert('Anda sudah pernah booking jadwal ini!'); window.location='daftar_jadwal.php';</script>";
    exit;
}

// --- Jika tombol submit ditekan ---
if (isset($_POST['submit'])) {

    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $dokter_id = $jadwal['dokter_id'];

    // Simpan booking baru
    $insert = mysqli_query($conn, "
        INSERT INTO booking (pasien_id, dokter_id, jadwal_id, status, catatan)
        VALUES ('$pasien_id', '$dokter_id', '$jadwal_id', 'menunggu', '$catatan')
    ");

    if ($insert) {
        echo "<script>alert('Booking berhasil! Menunggu persetujuan dokter.'); window.location='daftar_jadwal.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal booking, coba lagi.');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Booking Jadwal</title>
<link rel="stylesheet" href="asset/css/jadwal.css">
</head>
<body>

<div class="container" style="max-width:700px;margin-top:40px;">
    <div class="section">
        <h3>📝 Booking Konseling</h3>

        <div class="card">
            <h4>Dr. <?= htmlspecialchars($jadwal['nama_dokter']) ?></h4>
            <p>📅 <?= date('d F Y', strtotime($jadwal['tanggal'])) ?></p>
            <p>⏰ <?= $jadwal['jam'] ?></p>

            <form method="POST">
                <label>Catatan (opsional)</label>
                <textarea name="catatan" rows="4" placeholder="Tuliskan alasan atau tujuan konseling..." style="width:100%;margin-top:8px;padding:10px;border-radius:8px;border:1px solid #ccc;"></textarea>

                <button type="submit" name="submit" class="btn btn-booking" style="margin-top:10px;">Konfirmasi Booking</button>
                <a href="daftar_jadwal.php" class="btn btn-danger" style="background:#777;margin-left:5px;">Batal</a>
            </form>
        </div>
    </div>
</div>

</body>
</html>
