<?php
include 'connection.php';

// pastikan ada ID booking pada URL
if (!isset($_GET['booking_id'])) {
    die("<h2>ID Booking tidak ditemukan.</h2>");
}

$booking_id = intval($_GET['booking_id']);

// ambil detail booking
$sql = "
    SELECT b.booking_id, b.status AS status_booking, b.catatan,
           j.tanggal, j.jam,
           u.user_id AS dokter_id, u.nama AS nama_dokter, 
           u.email, u.no_hp
    FROM booking b
    JOIN jadwal_dokter j ON b.jadwal_id = j.jadwal_id
    JOIN users u ON j.dokter_id = u.user_id
    WHERE b.booking_id = '$booking_id'
";

$result = mysqli_query($conn, $sql);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    die("<h2>Data booking tidak ditemukan.</h2>");
}

$status = $book['status_booking'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Booking</title>
    <link rel="stylesheet" href="asset/css/style.css">
</head>

<body>
    <a href="daftar_jadwal.php" class="back-btn">
        &#8592; Kembali
    </a>

    <div class="detail-container">

        <h2 class="detail-title">Detail Booking</h2>

        <div class="detail-section">
            <p><strong>Tanggal Konseling:</strong> <?= date('d F Y', strtotime($book['tanggal'])) ?></p>
            <p><strong>Jam:</strong> <?= $book['jam'] ?></p>
            <p><strong>Status Booking:</strong> <?= ucfirst($book['status_booking']) ?></p>
            <p><strong>Catatan Anda:</strong> <?= $book['catatan'] ?></p>
        </div>

        <h3 class="detail-title" style="font-size:1.5rem;margin-top:25px;">Detail Dokter</h3>

        <div class="detail-section">
            <p><strong>Nama:</strong> <?= $book['nama_dokter'] ?></p>
            <p><strong>Email:</strong> <?= $book['email'] ?></p>
            <p><strong>No. HP:</strong> <?= $book['no_hp'] ?></p>
        </div>

        <?php if ($status == 'disetujui'): ?>
            <h3 class="detail-title" style="font-size:1.5rem;margin-top:25px;">Konsultasi</h3>

            <a class="btn-green"
                href="https://wa.me/<?= $book['no_hp'] ?>?text=Halo%20Dokter%20<?= urlencode($book['nama_dokter']) ?>..."
                target="_blank">
                Chat WhatsApp
            </a>

            <a class="btn-blue"
                href="mailto:<?= $book['email'] ?>?subject=Konsultasi%20Konseling">
                Kirim Email
            </a>

        <?php else: ?>
            <p class="info-note">Kontak hanya tersedia jika jadwal sudah disetujui dokter.</p>
        <?php endif; ?>

    </div>


</body>

</html>