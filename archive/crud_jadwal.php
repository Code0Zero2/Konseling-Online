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

// Ambil data jadwal dokter
$query = mysqli_query($conn, "
    SELECT jd.*, b.status AS booking_status, b.booking_id
    FROM jadwal_dokter jd
    LEFT JOIN booking b ON jd.jadwal_id = b.jadwal_id
    WHERE jd.dokter_id = '$dokter_id'
    ORDER BY jd.tanggal ASC, jd.jam ASC
");


?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Manajemen Jadwal Dokter</title>
<link rel="stylesheet" href="asset/css/jadwal.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>

<header>
    <nav class="navbar-crud">
        <ul>
            <li><a href="dokter_dashboard.php">Dashboard</a></li>
            <li><a href="dokter_jadwal.php" class="active">Jadwal</a></li>
            <li><a href="dokter_pasien.php">Pasien</a></li>
        </ul>
        <a href="logout.php"><button class="btn-primary-log">Logout</button></a>
    </nav>
</header>

<div class="container">

    <div class="section left">
        <h3>📅 Manajemen Jadwal Konseling</h3>

        <!-- <button class="btn btn-primary mb-3" onclick="openModal()">➕ Tambah Jadwal</button> -->
        <button class="btn btn-booking mb-3" onclick="openModal()">➕ Tambah Jadwal</button>


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
                                        <?= $row['booking_status']=='menunggu'?'bg-warning':
                                        ($row['booking_status']=='disetujui'?'bg-info':
                                        ($row['booking_status']=='selesai'?'bg-success':'bg-danger')) ?>">
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
                    <tr><td colspan="4" class="text-center">Belum ada jadwal.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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

</script>

</body>
</html>
