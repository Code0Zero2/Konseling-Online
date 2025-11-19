<?php
session_start();
include 'connection.php';

// Ambil ID pasien jika login
$pasien_id = isset($_SESSION['id_pasien']) ? $_SESSION['id_pasien'] : null;

// // Filter jadwal
// $filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
// $filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// // Pagination
// // $limit = jumlah baris per halaman (di sini 3 jadwal per halaman).
// // $page = nomor halaman saat ini (diambil dari ?page=). (int) meng-cast ke integer untuk mencegah string.
// // $start = offset untuk query SQL LIMIT start, limit.
// // Contoh:
// // Jika page=1: start = (1-1)*3 = 0 → ambil dari baris ke-0 sampai 2.
// // Jika page=2: start = (2-1)*3 = 3 → ambil dari baris ke-3 sampai 5.
// $limit = 3;
// $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
// $start = ($page - 1) * $limit;

// // Build filter query
// $filter_query = "";
// if ($filter_tanggal) $filter_query .= " AND j.tanggal = '$filter_tanggal'";
// if ($filter_status && $filter_status != 'semua') $filter_query .= " AND j.status = '$filter_status'";

// // Hitung total data untuk pagination
// $total_rows = mysqli_fetch_assoc(mysqli_query($conn, "
//     SELECT COUNT(*) AS total 
//     FROM jadwal_dokter j 
//     JOIN users u ON j.dokter_id = u.user_id
//     WHERE u.role='dokter' $filter_query
// "))['total'];

// $total_pages = ceil($total_rows / $limit);

// // Ambil jadwal dokter
// $query = mysqli_query($conn, "
//     SELECT j.*, u.nama AS nama_dokter
//     FROM jadwal_dokter j
//     JOIN users u ON j.dokter_id = u.user_id
//     WHERE u.role='dokter' $filter_query
//     ORDER BY j.tanggal ASC
//     LIMIT $start, $limit
// ");


// ---- FILTER ----
// pake ternary operator untuk set nilai filter
$filter_tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : '';
$filter_status  = isset($_GET['status'])  ? $_GET['status']  : '';

// ---- PAGINATION ----
$limit = 3; // tampil 3 data per halaman
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1; //cek apakah ada ?page= di URL terus dibubah jadi integer
$start = ($page - 1) * $limit;

// ---- MEMBUAT FILTER QUERY ----
$filter_query = "";

if (!empty($filter_tanggal)) {
    $filter_query .= " AND j.tanggal = '$filter_tanggal'";
}

if (!empty($filter_status) && $filter_status != 'semua') {
    $filter_query .= " AND j.status = '$filter_status'";
}

// ---- HITUNG TOTAL DATA ----
$sql_total = "
    SELECT COUNT(*) AS total
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    WHERE u.role='dokter' $filter_query
";

$result_total = mysqli_query($conn, $sql_total);
$total_rows   = mysqli_fetch_assoc($result_total)['total'];

$total_pages = ceil($total_rows / $limit);

// ---- AMBIL DATA JADWAL ----
$sql_jadwal = "
    SELECT j.*, u.nama AS nama_dokter
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    WHERE u.role='dokter' $filter_query
    ORDER BY j.tanggal ASC
    LIMIT $start, $limit
";

$query = mysqli_query($conn, $sql_jadwal);

// Jika login → Ambil monitoring & riwayat
if ($pasien_id) {
    $booking_query = mysqli_query($conn, "
        SELECT b.booking_id, j.tanggal, j.jam, j.status AS status_jadwal, j.dokter_id,
               u.nama AS nama_dokter, b.status AS status_booking
        FROM booking b
        JOIN jadwal_dokter j ON b.jadwal_id = j.jadwal_id
        JOIN users u ON j.dokter_id = u.user_id
        WHERE b.pasien_id = '$pasien_id'
        AND b.status IN ('menunggu','disetujui')
        ORDER BY j.tanggal DESC
    ");

    $query_history = mysqli_query($conn, "
        SELECT b.*, u.nama AS nama_dokter 
        FROM booking b
        JOIN users u ON b.dokter_id = u.user_id
        WHERE b.pasien_id = '$pasien_id'
        AND b.status IN ('selesai','dibatalkan')
    ");

} else {
    $booking_query = false;
    $query_history = false;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="asset/css/jadwal.css">
<title>Jadwal Konseling</title>

<script>
// AJAX batalkan booking
function batalkanBooking(bookingId) {
    if(confirm("Yakin ingin membatalkan jadwal ini?")) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "batalkan_booking.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function() {
            if (xhr.status === 200) {
                alert("Booking berhasil dibatalkan!");
                location.reload(); // ⬅ langsung refresh halaman
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
        <ul>
            <li><a href="index.php" >Beranda</a></li>
            <li><a href="about.html">Tentang</a></li>
            <li><a href="edukasi.php">Artikel</a></li>
            <li><a href="daftar_jadwal.php" class="active">Konseling</a></li>
        </ul>

        <?php if(isset($_SESSION['id_pasien'])): ?>
            <a href="profile.php" class="no-undlin">
              <button class="btn-primary-log">Profile</button>
            </a>
        <?php else: ?>
            <a href="signin.php" class="no-undlin">
              <button class="btn-primary-log">Masuk</button>
            </a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <!-- BAGIAN KIRI: JADWAL -->
    <div class="section left">
        <h3>🩺 Jadwal Konseling</h3>

        <!-- Filter Box -->
        <div class="filter-box">
            <form method="GET">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= $filter_tanggal ?>">

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

        <!-- LIST JADWAL -->
        <?php if (mysqli_num_rows($query) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <div class="card">
                <h4>Dr. <?= $row['nama_dokter'] ?></h4>
                <p>📅 <?= date('d F Y', strtotime($row['tanggal'])) ?> — ⏰ <?= $row['jam'] ?></p>

                <?php if ($row['status'] == 'tersedia'): ?>

                    <?php if ($pasien_id): ?>
                        <a href="booking.php?jadwal_id=<?= $row['jadwal_id'] ?>" class="btn btn-booking">Booking</a>
                    <?php else: ?>
                        <a href="signin.php" class="btn btn-booking">Login untuk Booking</a>
                    <?php endif; ?>

                <?php else: ?>
                    <p class="status penuh">❌ Penuh</p>
                    <button class="btn btn-disabled">Penuh</button>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-data">Tidak ada jadwal ditemukan.</p>
        <?php endif; ?>

        <!-- Pagination -->
        <div class="pagination">
            <a href="?page=<?= $page-1 ?>" class="<?= ($page<=1)?'disabled':'' ?>">Prev</a>

            <?php for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?page=<?= $i ?>" class="<?= ($page==$i)?'active':'' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <a href="?page=<?= $page+1 ?>" class="<?= ($page>=$total_pages)?'disabled':'' ?>">Next</a>
        </div>
    </div>

    <!-- BAGIAN KANAN: MONITORING -->
    <div class="section right">
        <h3>📋 Monitoring Jadwal Saya</h3>

        <?php if ($pasien_id && mysqli_num_rows($booking_query) > 0): ?>
            <?php while($book = mysqli_fetch_assoc($booking_query)): ?>
                <div class="card" id="booking-<?= $book['booking_id'] ?>">
                    <h4>Dr. <?= $book['nama_dokter']; ?></h4>
                    <p>📅 <?= date('d F Y', strtotime($book['tanggal'])) ?> — ⏰ <?= $book['jam'] ?></p>

                    <p>Status:
                        <span class="badge <?= $book['status_booking']=='menunggu'?'bg-warning':($book['status_booking']=='selesai'?'bg-success':'bg-danger') ?>">
                            <?= ucfirst($book['status_booking']) ?>
                        </span>
                    </p>

                    <a href="detail_booking.php?booking_id=<?= $book['booking_id'] ?>" class="btn btn-detail">Detail Booking</a>
                    
                    <?php if ($book['status_booking'] != 'selesai'): ?>
                        <button onclick="batalkanBooking(<?= $book['booking_id'] ?>)" class="btn btn-batal">Batalkan</button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <p class="no-data">Belum ada jadwal yang dibooking.</p>
        <?php endif; ?>
    </div>
</div>

<!-- BAGIAN HISTORY -->
<div class="history">
    <h3>Riwayat Konseling</h3>

    <?php if ($pasien_id && mysqli_num_rows($query_history) > 0): ?>
        <?php while($h = mysqli_fetch_assoc($query_history)): ?>
            <div class="card">
                <h4>Dr. <?=$h['nama_dokter'] ?></h4>
                <p class="status"><?= ucfirst($h['status']) ?></p>
            </div>
        <?php endwhile; ?>

    <?php else: ?>
        <p class="no-data">Belum ada riwayat konseling.</p>
    <?php endif; ?>
</div>

</body>
</html>
