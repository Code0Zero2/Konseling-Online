<?php
include 'connection.php';
session_start();

// contoh user login sementara
$pasien_id = 4;

// Filter jadwal
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Pagination
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Filter query
$filter_query = "";
if ($filter_tanggal) $filter_query .= " AND j.tanggal = '$filter_tanggal'";
if ($filter_status && $filter_status != 'semua') $filter_query .= " AND j.status = '$filter_status'";

// Hitung total data
$total_rows = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM jadwal_dokter j 
    JOIN users u ON j.dokter_id = u.user_id
    WHERE u.role='dokter' $filter_query
"))['total'];
$total_pages = ceil($total_rows / $limit);

// Query jadwal dokter
$query = mysqli_query($conn, "
    SELECT j.*, u.nama AS nama_dokter
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    WHERE u.role='dokter' $filter_query
    ORDER BY j.tanggal ASC
    LIMIT $start, $limit
");

// Query booking user
$booking_query = mysqli_query($conn, "
    SELECT b.booking_id, j.tanggal, j.jam, j.status AS status_jadwal, 
           u.nama AS nama_dokter, b.status AS status_booking
    FROM booking b
    JOIN jadwal_dokter j ON b.jadwal_id = j.jadwal_id
    JOIN users u ON j.dokter_id = u.user_id
    WHERE b.pasien_id = '$pasien_id'
    ORDER BY j.tanggal DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="asset/css/jadwal.css">
<title>Jadwal Konseling</title>
<script>
// --- AJAX batalkan booking ---
function batalkanBooking(bookingId) {
    if(confirm("Yakin ingin membatalkan jadwal ini?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "batalkan_booking.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if(xhr.status === 200) {
                document.getElementById("booking-"+bookingId).remove();
                alert("Booking berhasil dibatalkan!");
            }
        };
        xhr.send("booking_id=" + bookingId);
    }
}
</script>
</head>
<body>

<header>
        <nav class="navbar">
            <!-- <img src="assets/img/logo.png" alt="Logo" class="logo"> -->
            <ul>
                <li><a href="index.php" >Beranda</a></li>
                <li><a href="about.html">Tentang</a></li>
                <li><a href="edukasi.php">Artikel</a></li>
                <li><a href="daftar_jadwal.php" class="active">Konseling</a></li>
                <li><a href="signin.php"><button class="btn-primary">Masuk</button></a></li>
            </ul>
        </nav>
    </header>

<div class="container">
    <!-- BAGIAN KIRI: JADWAL -->
    <div class="section left">
        <h3>🩺 Jadwal Konseling</h3>
        <div class="filter-box">
            <form method="GET">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= htmlspecialchars($filter_tanggal) ?>">

                <label>Status</label>
                <select name="status">
                    <option value="semua">Semua</option>
                    <option value="tersedia" <?= $filter_status=='tersedia'?'selected':'' ?>>Tersedia</option>
                    <option value="penuh" <?= $filter_status=='penuh'?'selected':'' ?>>Penuh</option>
                </select>

                <button class="filter-btn" type="submit">Filter</button>
                <a href="daftar_jadwal.php" class="reset-btn">Reset</a>
            </form>
        </div>

        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <div class="card">
                <h4>Dr. <?= htmlspecialchars($row['nama_dokter']) ?></h4>
                <p>📅 <?= date('d F Y', strtotime($row['tanggal'])) ?> — ⏰ <?= htmlspecialchars($row['jam']) ?></p>
                <?php if ($row['status'] == 'tersedia'): ?>
                    <p class="status tersedia">✔ Tersedia</p>
                    <a href="booking.php?jadwal_id=<?= $row['jadwal_id'] ?>" class="btn btn-booking">Booking</a>
                <?php else: ?>
                    <p class="status penuh">❌ Penuh</p>
                    <a href="#" class="btn btn-disabled">Penuh</a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-data">Tidak ada jadwal ditemukan.</p>
        <?php endif; ?>

        <div class="pagination">
            <a href="?page=<?= $page-1 ?>" class="<?= ($page<=1)?'disabled':'' ?>">Prev</a>
            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= ($page==$i)?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>
            <a href="?page=<?= $page+1 ?>" class="<?= ($page>=$total_pages)?'disabled':'' ?>">Next</a>
        </div>
    </div>

    <!-- BAGIAN KANAN: MONITOR -->
    <div class="section right">
        <h3>📋 Monitoring Jadwal Saya</h3>
        <?php if (mysqli_num_rows($booking_query) > 0): ?>
            <?php while($book = mysqli_fetch_assoc($booking_query)): ?>
            <div class="card">
                <h4>Dr. <?= htmlspecialchars($book['nama_dokter']) ?></h4>
                <p>📅 <?= date('d F Y', strtotime($book['tanggal'])) ?> — ⏰ <?= htmlspecialchars($book['jam']) ?></p>
                <p>Status:
                    <span class="badge <?= $book['status_booking']=='menunggu'?'bg-warning':($book['status_booking']=='selesai'?'bg-success':'bg-danger') ?>">
                        <?= ucfirst($book['status_booking']) ?>
                    </span>
                </p>
                <a href="detail_dokter.php?nama=<?= urlencode($book['nama_dokter']) ?>" class="btn btn-detail">Detail Dokter</a>
                <?php if ($book['status_booking'] != 'selesai'): ?>
                    <a href="batal_booking.php?id=<?= $book['booking_id'] ?>" class="btn btn-batal" onclick="return confirm('Batalkan booking ini?')">Batalkan</a>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-data">Belum ada jadwal yang dibooking.</p>
        <?php endif; ?>
    </div>
</div>

    <!-- Bagian History -->
    <div class="history">
        <h3>Riwayat Konseling</h3>
        <?php
        $query_history = mysqli_query($conn, "
            SELECT b.*, u.nama AS nama_dokter, 
            FROM booking b
            JOIN users u ON b.dokter_id = u.user_id
            WHERE b.pasien_id = '$pasien_id' AND b.status IN ('selesai','dibatalkan')
        ");
        while($h = mysqli_fetch_assoc($query_history)): ?>
        <div class="card">
            <h4>Dr. <?= htmlspecialchars($h['nama_dokter']) ?></h4>
            <!-- <p>📅 <?= date('d F Y', strtotime($h['tanggal'])) ?> - ⏰ <?= $h['jam'] ?></p> -->
            <p class="status"><?= ucfirst($h['status']) ?></p>
        </div>
        <?php endwhile; ?>
    </div>


</body>
</html>
