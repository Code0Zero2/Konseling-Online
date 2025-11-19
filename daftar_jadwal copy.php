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



<!-- olddddddd -->

<?php
include 'connection.php';

// --- Konfigurasi pagination ---
$limit = 3; // jumlah dokter per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// --- Hitung total data ---
$result_total = mysqli_query($conn, "
    SELECT COUNT(*) AS total 
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    WHERE u.role='dokter'
");
$total_rows = mysqli_fetch_assoc($result_total)['total'];
$total_pages = ceil($total_rows / $limit);

// --- Query data dokter + jadwal ---
$query = mysqli_query($conn, "
    SELECT j.jadwal_id, j.dokter_id, j.tanggal, j.jam, j.status, 
           u.nama AS nama_dokter
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    WHERE u.role='dokter'
    GROUP BY j.jadwal_id
    ORDER BY j.tanggal ASC
    LIMIT $start, $limit
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/css/style.css">
    <title>Jadwal-Konseling</title>
    <style>
    .container-jadwal{
        width: 90%;
        max-width: 1000px;
        margin: 30px auto;
        border: 1px solid rgb(114, 114, 114) ;
        box-shadow: 5px 5px rgba(131, 131, 131, 0.37);
        border-radius: 10px;
    }

    .tab-con{
        padding: 50px;
    }
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.62);
        padding: 20px;
        margin-bottom: 20px;
    }
    .card h3 {
        margin: 0;
        color: #333;
    }
    .info {
        margin-top: 10px;
        font-size: 14px;
    }
    .status {
        font-weight: bold;
        display: inline-block;
        margin-top: 10px;
    }
    .tersedia {
        color: #28a745;
    }
    .penuh {
        color: #dc3545;
    }
    .btn {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: bold;
        text-decoration: none;
        margin-top: 10px;
    }
    .btn-booking {
        background: #007bff;
        color: white;
    }
    .btn-disabled {
        background: #ccc;
        color: #666;
        cursor: not-allowed;
    }
    /* Pagination */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 30px;
        gap: 10px;
    }
    .pagination a {
        text-decoration: none;
        color: #007bff;
        border: 1px solid #007bff;
        padding: 6px 12px;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .pagination a:hover {
        background: #007bff;
        color: white;
    }
    .pagination .active {
        background: #007bff;
        color: white;
    }
    .pagination .disabled {
        color: #aaa;
        border-color: #ccc;
        pointer-events: none;
    }
    </style>
</head>
<body>
    <header>
        <nav class="navbar">
            <!-- <img src="assets/img/logo.png" alt="Logo" class="logo"> -->
            <ul>
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="about.html">Tentang</a></li>
                <li><a href="edukasi.php">Artikel</a></li>
                <li><a href="daftar_jadwal.php">Konseling</a></li>
                <li><a href="signin.php"><button class="btn-primary">Masuk</button></a></li>
            </ul>
        </nav>
    </header>
    <h3 style="display:flex; justify-content: center;">Jadwal Konseling</h3>
    <div class="container-jadwal">
        <div class="tab-con">

            <?php while($row = mysqli_fetch_assoc($query)): ?>
            <div class="card">
                <h3>Dr. <?= $row['nama_dokter'] ?></h3>
                <!-- <div class="spesialis">Konseling & Psikologi</div> -->
    
               
    
                <div class="info">
                    <p>📅 <?= date('d F Y', strtotime($row['tanggal'])) ?></p>
                </div>
    
                <?php if ($row['status'] == 'tersedia'): ?>
                    <div class="status tersedia">✔ Tersedia</div><br>
                    <a href="booking.php?jadwal_id=<?= $row['jadwal_id'] ?>" class="btn btn-booking">Booking</a>
                <?php else: ?>
                    <div class="status penuh">❌ Penuh</div><br>
                    <a href="#" class="btn btn-disabled">Penuh</a>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        </div>
    </div>
    <div class="pagination">
        <a class="<?= ($page <= 1) ? 'disabled' : '' ?>" href="?page=<?= $page-1 ?>">Previous</a>
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a class="<?= ($page == $i) ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
        <a class="<?= ($page >= $total_pages) ? 'disabled' : '' ?>" href="?page=<?= $page+1 ?>">Next</a>
    </div>
</body>
</html>


<div class="tab-con">
            <table class="t-jadwal">
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Dokter</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                <?php
                    include 'connection.php';
                    $query = mysqli_query($conn, "SELECT * FROM jadwal_dokter");
                    $nomor = 0;
                    while ($data = mysqli_fetch_array($query)) {
                        $nomor += 1;
                ?>
                <tr>
                    <td style="text-align:center;"><?=$nomor?></td>
                    <td><?=$data['tanggal']?></td>
                    <td><?=$data['jam']?></td>
                    <td></td>
                    <td><?=$data['status']?></td>
                    <td style="text-align:center;"><a href=""><button>Daftar</button></a></td>
                </tr>
                <?php } ?>
            </table>
        </div>



        <!-- versiiiiii harus login -->
                
<!-- newwwww -->
 <?php
 session_start();
// if(!isset($_SESSION['id_pasien'])){
//     // header("Location: signin.php");
//     // exit();
// }
include 'connection.php';
// session_start();

// contoh user login sementara
// $pasien_id = $_SESSION['id_pasien'];
$pasien_id = isset($_SESSION['id_pasien']) ? $_SESSION['id_pasien'] : null;


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
// $booking_query = mysqli_query($conn, "
//     SELECT b.booking_id, j.tanggal, j.jam, j.status AS status_jadwal, 
//            u.nama AS nama_dokter, b.status AS status_booking
//     FROM booking b
//     JOIN jadwal_dokter j ON b.jadwal_id = j.jadwal_id
//     JOIN users u ON j.dokter_id = u.user_id
//     WHERE b.pasien_id = '$pasien_id'
//     ORDER BY j.tanggal DESC
// ");

if ($pasien_id) {

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

    // Query history
    $query_history = mysqli_query($conn, "
        SELECT b.*, u.nama AS nama_dokter 
        FROM booking b
        JOIN users u ON b.dokter_id = u.user_id
        WHERE b.pasien_id = '$pasien_id' 
        AND b.status IN ('selesai','dibatalkan')
    ");

} else {
    // Jika belum login, buat kosong
    $booking_query = false;
    $query_history = false;
}
?>
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
            </ul>
            <a href="signin.php" class="no-undlin">
              <button class="btn-primary-log">Masuk</button>
            </a>
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
    <!-- <div class="section right">
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
    </div> -->

    <div class="section right">
    <h3>📋 Monitoring Jadwal Saya</h3>

    <?php if ($pasien_id && mysqli_num_rows($booking_query) > 0): ?>
        <?php while($book = mysqli_fetch_assoc($booking_query)): ?>
            <!-- tampilkan kartu booking -->
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
        // $query_history = mysqli_query($conn, "
        //     SELECT b.*, u.nama AS nama_dokter 
        //     FROM booking b
        //     JOIN users u ON b.dokter_id = u.user_id
        //     WHERE b.pasien_id = '$pasien_id' AND b.status IN ('selesai','dibatalkan')
        // ");
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
