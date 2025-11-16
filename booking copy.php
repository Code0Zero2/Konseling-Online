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
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Daftar Dokter</title>
<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f4f9;
        margin: 0;
        padding: 30px;
    }
    .container {
        max-width: 800px;
        margin: auto;
    }
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        padding: 20px;
        margin-bottom: 20px;
    }
    .card h3 {
        margin: 0;
        color: #333;
    }
    .spesialis {
        color: #777;
        font-size: 14px;
    }
    .rating {
        background: #007bff;
        color: white;
        padding: 3px 7px;
        border-radius: 8px;
        font-size: 13px;
        display: inline-block;
        margin-top: 8px;
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

<div class="container">
    <h2>Daftar Jadwal Dokter</h2>

    <?php while($row = mysqli_fetch_assoc($query)): ?>
        <div class="card">
            <h3>Dr. <?= $row['nama_dokter'] ?></h3>
            <div class="spesialis">Konseling & Psikologi</div>

            <div class="rating">⭐ <?= number_format($row['rata_rating'] ?? 0, 1) ?></div>

            <div class="info">
                <p>📅 <?= date('d F Y', strtotime($row['tanggal'])) ?></p>
                <p>⏰ <?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?></p>
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

    <!-- Pagination -->
    <div class="pagination">
        <a class="<?= ($page <= 1) ? 'disabled' : '' ?>" href="?page=<?= $page-1 ?>">Previous</a>
        <?php for($i=1; $i<=$total_pages; $i++): ?>
            <a class="<?= ($page == $i) ? 'active' : '' ?>" href="?page=<?= $i ?>"><?= $i ?></a>
        <?php endfor; ?>
        <a class="<?= ($page >= $total_pages) ? 'disabled' : '' ?>" href="?page=<?= $page+1 ?>">Next</a>
    </div>

</div>
</body>
</html>
