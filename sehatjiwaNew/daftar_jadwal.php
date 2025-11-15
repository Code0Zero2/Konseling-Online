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