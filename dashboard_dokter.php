<?php
session_start();
include 'connection.php';

// Cek login dokter
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'dokter') {
    header("Location: signin.php");
    exit;
}

// $dokter_id = isset($_SESSION['id_dokter']) ? $_SESSION['id_dokter'] : null;

$dokter_id = $_SESSION['user_id'];

// === 1. Konseling Bulan Ini (menggunakan join jadwal_dokter) ===
$q1 = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM booking b
    JOIN jadwal_dokter j ON b.jadwal_id = j.jadwal_id
    WHERE b.dokter_id='$dokter_id'
      AND MONTH(j.tanggal) = MONTH(CURRENT_DATE())
      AND YEAR(j.tanggal) = YEAR(CURRENT_DATE())
");
$konseling_bulan = mysqli_fetch_assoc($q1)['total'];

// === 2. Total Pasien Unik ===
$q2 = mysqli_query($conn, "
    SELECT COUNT(DISTINCT pasien_id) AS total
    FROM booking
    WHERE dokter_id='$dokter_id'
");
$total_pasien = mysqli_fetch_assoc($q2)['total'];

// === 3. Status selesai ===
$q3 = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM booking
    WHERE dokter_id='$dokter_id'
      AND status='selesai'
");
$selesai = mysqli_fetch_assoc($q3)['total'];

// === 4. Status dibatalkan ===
$q4 = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM booking
    WHERE dokter_id='$dokter_id'
      AND status='dibatalkan'
");
$batal = mysqli_fetch_assoc($q4)['total'];

// === 5. Rata-rata rating ===
$q5 = mysqli_query($conn, "
    SELECT ROUND(AVG(rating),1) AS avg_rating
    FROM rating_testimoni
    WHERE dokter_id='$dokter_id'
");
$avg_rating = mysqli_fetch_assoc($q5)['avg_rating'] ?? 0;

// === 6. Total testimoni ===
$q6 = mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM rating_testimoni
    WHERE dokter_id='$dokter_id'
");
$total_testimoni = mysqli_fetch_assoc($q6)['total'];

// === 7. Grafik: Konseling per bulan ===
$q7 = mysqli_query($conn, "
    SELECT MONTH(j.tanggal) AS bulan, COUNT(*) AS jumlah
    FROM booking b
    JOIN jadwal_dokter j ON b.jadwal_id = j.jadwal_id
    WHERE b.dokter_id='$dokter_id'
    GROUP BY MONTH(j.tanggal)
    ORDER BY bulan ASC
");

$bulan = [];
$jumlah = [];

while ($row = mysqli_fetch_assoc($q7)) {
    $bulan[] = $row['bulan'];
    $jumlah[] = $row['jumlah'];
}


// Ambil data jadwal dokter
$query = mysqli_query($conn, "
    SELECT jd.*, b.status AS booking_status, b.booking_id
    FROM jadwal_dokter jd
    LEFT JOIN booking b ON jd.jadwal_id = b.jadwal_id
    WHERE jd.dokter_id = '$dokter_id'
    AND jd.status = 'tersedia'
    AND (
        b.status IN ('menunggu', 'disetujui') 
        OR b.status IS NULL
    )
    ORDER BY jd.tanggal ASC, jd.jam ASC
");
$history = mysqli_query($conn, "
    SELECT jd.*, b.status AS booking_status, b.booking_id
    FROM jadwal_dokter jd
    LEFT JOIN booking b ON jd.jadwal_id = b.jadwal_id
    WHERE jd.dokter_id = '$dokter_id'
    AND b.status IN ('selesai', 'dibatalkan')
    ORDER BY jd.tanggal DESC, jd.jam DESC
");


?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="asset/css/jadwal.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/css/navdas.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <header class="my-header">
        <nav class="my-navbar">
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="about.php">Tentang</a></li>
                <li><a href="edukasi.php">Artikel</a></li>
                <li><a href="dashboard_dokter.php" class="active">Dashboard</a></li>
            </ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- <a href="logout.php" class="no-undlin">
          <button class="btn-primary-log">Logout</button>
        </a> -->
                <a href="logout.php" class="btn-primary-log">Logout</a>
            <?php else: ?>
                <!-- <a href="signin.php" class="no-undlin">
          <button class="btn-primary-log">Masuk</button>
        </a> -->
                <a href="signin.php" class="btn-primary-log">Masuk</a>
            <?php endif; ?>
        </nav>
    </header>
    <div style="text-align: center;">

        <h2 class="salam">Hai, <?= $_SESSION['nama'] ?> <span class="wave">🖐</span></h2>
        </div>
    <div class="stats-doc">

        <div class="stats-container">

            <div class="card">
                <h3>Konseling Bulan Ini</h3>
                <p><?= $konseling_bulan ?></p>
            </div>

            <div class="card">
                <h3>Total Pasien</h3>
                <p><?= $total_pasien ?></p>
            </div>

            <div class="card">
                <h3>Selesai</h3>
                <p><?= $selesai ?></p>
            </div>

            <div class="card">
                <h3>Dibatalkan</h3>
                <p><?= $batal ?></p>
            </div>

            <div class="card">
                <h3>Rata-rata Rating</h3>
                <p><?= $avg_rating ?> ⭐</p>
            </div>

            <div class="card">
                <h3>Total Testimoni</h3>
                <p><?= $total_testimoni ?></p>
            </div>

        </div>

        <div class="charts-container">
            <div class="chart-card">
                <h3>Grafik Konseling per Bulan</h3>
                <canvas id="chartKonseling"></canvas>
            </div>

            <div class="chart-card">
                <h3>Persentase Status Konseling</h3>
                <canvas id="chartStatus"></canvas>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="section left">
            <h3>📅 Manajemen Jadwal Konseling</h3>

            <!-- <button class="btn btn-primary mb-3" onclick="openModal()">➕ Tambah Jadwal</button> -->
            <button class="btn btn-booking mb-3" onclick="openModal()">➕ Tambah Jadwal</button>

            <div class="table-container">

                <table class="table custom-table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($query) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($query)): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= $row['jam'] ?></td>
                                    <td>
                                        <?php if ($row['booking_status']): ?>
                                            <span class="badge 
                                            <?= $row['booking_status'] == 'menunggu' ? 'bg-warning' : ($row['booking_status'] == 'disetujui' ? 'bg-info' : ($row['booking_status'] == 'selesai' ? 'bg-success' : 'bg-danger')) ?>">
                                                <?= ucfirst($row['booking_status']) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Belum Dibooking</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if ($row['booking_id']): ?>
                                            <button
                                                class="btn btn-sm btn-warning"
                                                onclick="openStatusModal(<?= $row['booking_id'] ?>, '<?= $row['booking_status'] ?>')">
                                                Ubah Status
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn btn-sm btn-success"
                                            onclick="editJadwal(<?= $row['jadwal_id'] ?>, '<?= $row['tanggal'] ?>', '<?= $row['jam'] ?>')">
                                            Edit
                                        </button>

                                        <a href="hapus_jadwal.php?id=<?= $row['jadwal_id'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus jadwal?')">
                                            Hapus
                                        </a>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada jadwal.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="section right">
            <h3>📘 Riwayat Konseling (History)</h3>
            <div class="table-container">
                <table class="table custom-table table-hover">
                    <thead class="table-secondary">
                        <tr>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (mysqli_num_rows($history) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($history)): ?>
                                <tr>
                                    <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                    <td><?= $row['jam'] ?></td>
                                    <td>
                                        <span class="badge 
                                    <?= $row['booking_status'] == 'selesai' ? 'bg-success' : 'bg-danger' ?>">
                                            <?= ucfirst($row['booking_status']) ?>
                                        </span>
                                    </td>

                                    <td>
                                        <a href="hapus_jadwal.php?id=<?= $row['booking_id'] ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus riwayat ini?')">
                                            Hapus
                                        </a>
                                        <!-- <a href="detail_booking.php?id=<?= $row['booking_id'] ?>"
                                            class="btn btn-sm btn-info">
                                            Detail
                                        </a> -->
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada history.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>



    <!-- MODAL TAMBAH / EDIT -->
    <div class="modal" id="jadwalModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="simpan_jadwal.php" class="modal-content">

                <input type="hidden" id="jadwal_id" name="jadwal_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Jadwal</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>

                <div class="modal-body">
                    <label>Tanggal</label>
                    <input type="date" id="tanggal" name="tanggal" required class="form-control mb-2">

                    <label>Jam</label>
                    <input type="time" id="jam" name="jam" required class="form-control mb-2">
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>

    <!-- MODAL UBAH STATUS -->
    <div class="modal" id="statusModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="update_statbook.php" class="modal-content">

                <input type="hidden" id="booking_id" name="booking_id">

                <div class="modal-header">
                    <h5 class="modal-title">Ubah Status Booking</h5>
                    <button type="button" class="btn-close" onclick="closeStatusModal()"></button>
                </div>

                <div class="modal-body">
                    <label>Status Booking</label>
                    <select id="status" name="status" class="form-control" required>
                        <option value="menunggu">Menunggu</option>
                        <option value="disetujui">Disetujui</option>
                        <option value="selesai">Selesai</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>
        </div>
    </div>


    <script>
        function openModal() {
            document.getElementById("jadwal_id").value = "";
            document.getElementById("modalTitle").innerText = "Tambah Jadwal";
            document.getElementById("jadwalModal").style.display = "block";
        }

        function closeModal() {
            document.getElementById("jadwalModal").style.display = "none";
        }

        function editJadwal(id, tanggal, jam) {
            document.getElementById("jadwal_id").value = id;
            document.getElementById("tanggal").value = tanggal;
            document.getElementById("jam").value = jam;

            document.getElementById("modalTitle").innerText = "Edit Jadwal";
            document.getElementById("jadwalModal").style.display = "block";
        }

        function openStatusModal(id, status) {
            document.getElementById("booking_id").value = id;
            document.getElementById("status").value = status;
            document.getElementById("statusModal").style.display = "block";
        }

        function closeStatusModal() {
            document.getElementById("statusModal").style.display = "none";
        }

        new Chart(document.getElementById('chartKonseling'), {
            type: 'bar',
            data: {
                labels: <?= json_encode($bulan) ?>,
                datasets: [{
                    label: 'Jumlah Konseling',
                    data: <?= json_encode($jumlah) ?>,
                    borderWidth: 1,
                    backgroundColor: '#007bff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true // penting, biar chart tidak gepeng
            }
        });

        new Chart(document.getElementById('chartStatus'), {
            type: 'doughnut',
            data: {
                labels: ['Selesai', 'Dibatalkan'],
                datasets: [{
                    data: [<?= $selesai ?>, <?= $batal ?>],
                    backgroundColor: ['#28a745', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });
    </script>

</body>

</html>