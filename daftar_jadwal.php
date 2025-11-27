<?php
session_start();
include 'connection.php';

// $link_beranda = 'index.php';

// if(isset($_SESSION['id_pasien'])){
//   $id_user = $_SESSION['id_pasien'];

//   $cek = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"); // ambil role user buat cek role
//   $data_role = mysqli_fetch_assoc($cek);

//   if($data_role && $data_role['role'] == 'dokter'){
//     $link_beranda = 'dashboard_dokter.php';
//   }
// }
// Ambil ID pasien jika login
$pasien_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


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
    LEFT JOIN booking b ON j.jadwal_id = b.jadwal_id
    WHERE u.role='dokter'
    AND j.status = 'tersedia'
    AND b.booking_id IS NULL
    $filter_query
";


$result_total = mysqli_query($conn, $sql_total);
$total_rows   = mysqli_fetch_assoc($result_total)['total'];

$total_pages = ceil($total_rows / $limit);

// ---- AMBIL DATA JADWAL ----
$sql_jadwal = "
    SELECT j.*, u.nama AS nama_dokter
    FROM jadwal_dokter j
    JOIN users u ON j.dokter_id = u.user_id
    LEFT JOIN booking b ON j.jadwal_id = b.jadwal_id
    WHERE u.role='dokter'
    AND j.status = 'tersedia'
    AND b.booking_id IS NULL
    $filter_query
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
    <link rel="stylesheet" href="asset/css/modal_testi.css">
    <title>Jadwal Konseling</title>

    <script>
        // AJAX batalkan booking
        function batalkanBooking(bookingId) {
            if (confirm("Yakin ingin membatalkan jadwal ini?")) {
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

<?php if (isset($_SESSION['logout_success'])): ?>
    <div id="popup-logout" class="popup-overlay">
        <div class="popup-box">
            <h3>Berhasil Logout</h3>
            <p>Kamu telah keluar dari akun.</p>
            <button id="closePopup">Tutup</button>
        </div>
    </div>

    <script>
        document.getElementById("closePopup").addEventListener("click", function() {
            document.getElementById("popup-logout").style.display = "none";
        });
    </script>

<?php unset($_SESSION['logout_success']);
endif; ?>

<body>

    <header>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="about.php">Tentang</a></li>
                <li><a href="edukasi.php">Artikel</a></li>
                <li><a href="daftar_jadwal.php" class="active">Konseling</a></li>
                <li><a href="layanan_aduan.php">Layanan aduan</a></li>
            </ul>

            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profil.php" class="no-undlin">
                    <button class="btn-primary-log">Profil</button>
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
                        <option value="tersedia" <?= $filter_status == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                        <option value="penuh" <?= $filter_status == 'penuh' ? 'selected' : '' ?>>Penuh</option>
                    </select>

                    <button class="filter-btn" type="submit">Filter</button>
                    <a href="daftar_jadwal.php" class="reset-btn">Reset</a>
                </form>
            </div>

            <!-- LIST JADWAL -->
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <div class="card">
                        <h4><?= $row['nama_dokter'] ?></h4>
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
                <a href="?page=<?= $page - 1 ?>" class="<?= ($page <= 1) ? 'disabled' : '' ?>">Prev</a>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?= $i ?>" class="<?= ($page == $i) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <a href="?page=<?= $page + 1 ?>" class="<?= ($page >= $total_pages) ? 'disabled' : '' ?>">Next</a>
            </div>
        </div>

        <!-- BAGIAN KANAN: MONITORING -->
        <div class="section right">
            <h3>📋 Monitoring Jadwal Saya</h3>
            <div class="scroll">

                <?php if ($pasien_id && mysqli_num_rows($booking_query) > 0): ?>
                    <?php while ($book = mysqli_fetch_assoc($booking_query)): ?>
                        <div class="card" id="booking-<?= $book['booking_id'] ?>">
                            <h4> <?= $book['nama_dokter']; ?></h4>
                            <p>📅 <?= date('d F Y', strtotime($book['tanggal'])) ?> — ⏰ <?= $book['jam'] ?></p>

                            <p>Status:
                                <span class="badge <?= $book['status_booking'] == 'menunggu' ? 'bg-warning' : ($book['status_booking'] == 'selesai' ? 'bg-success' : 'bg-danger') ?>">
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
    </div>

    <!-- BAGIAN HISTORY -->
    <div class="history">
        <h3>Riwayat Konseling</h3>

        <?php if ($pasien_id && mysqli_num_rows($query_history) > 0): ?>
            <?php while ($h = mysqli_fetch_assoc($query_history)): ?>
                <div class="card">
                    <h4>Dr. <?= $h['nama_dokter'] ?></h4>
                    <p class="status"><?= ucfirst($h['status']) ?></p>

                    <?php if ($h['status'] == 'selesai'): ?>
                        <button class="btn btn-booking"
                            onclick="openModalTestimoni(<?= $h['booking_id'] ?>)">
                            ⭐ Beri Testimoni & Rating
                        </button>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>

        <?php else: ?>
            <p class="no-data">Belum ada riwayat konseling.</p>
        <?php endif; ?>
    </div>

    <!-- BACKDROP -->
    <div id="modalBackdrop" class="modal-backdrop"></div>

    <!-- MODAL TESTIMONI -->
    <div id="testimoniModal" class="custom-modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">⭐ Beri Testimoni & Rating</h5>
                    <button type="button" class="close" onclick="closeModalTestimoni()">&times;</button>
                </div>

                <form id="formTestimoni">
                    <input type="hidden" id="booking_id_testi" name="booking_id">

                    <div class="modal-body">
                        <label>Rating (1–5)</label>
                        <select name="rating" class="form-control mb-2" required>
                            <option value="5">⭐ 5 - Sangat Baik</option>
                            <option value="4">⭐ 4 - Baik</option>
                            <option value="3">⭐ 3 - Cukup</option>
                            <option value="2">⭐ 2 - Kurang</option>
                            <option value="1">⭐ 1 - Buruk</option>
                        </select>
                        <br>
                        <label>Testimoni</label><br>
                        <textarea name="pesan" class="form-control mb-2" required></textarea>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="modal-btn modal-btn-secondary" onclick="closeModalTestimoni()">Batal</button>
                        <button type="submit" class="modal-btn modal-btn-primary">Kirim</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <script>
        function openModalTestimoni(bookingId) {
            document.getElementById("booking_id_testi").value = bookingId;

            document.getElementById("modalBackdrop").classList.add("show");
            document.getElementById("testimoniModal").classList.add("show");

            document.body.classList.add("modal-open");
        }

        function closeModalTestimoni() {
            document.getElementById("modalBackdrop").classList.remove("show");
            document.getElementById("testimoniModal").classList.remove("show");

            document.body.classList.remove("modal-open");
        }


        document.getElementById("formTestimoni").addEventListener("submit", function(e) {
            e.preventDefault();

            let formData = new FormData(this);

            fetch("simpan_testimoni.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.text())
                .then(response => {

                    response = response.trim(); // pastikan tidak ada spasi/enter

                    if (response === "INSERT_SUCCESS") {
                        alert("Terima kasih! Testimoni Anda berhasil disimpan.");
                        closeModalTestimoni();
                        location.reload();

                    } else if (response === "UPDATE_SUCCESS") {
                        alert("Testimoni Anda berhasil diperbarui.");
                        closeModalTestimoni();
                        location.reload();

                    } else {
                        console.error("Gagal:", response);
                        alert("Gagal mengirim testimoni.\nDetail: " + response);
                    }

                })
                .catch(err => {
                    console.error("Error:", err);
                    alert("Terjadi kesalahan koneksi saat mengirim testimoni.");
                });
        });
    </script>


</body>

</html>