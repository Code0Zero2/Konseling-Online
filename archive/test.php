<!-- artikel detail old -->
 <?php
include 'connection.php';

// Cek apakah ada ID artikel
if (!isset($_GET['id'])) {
    header("Location: edukasi.php");
}

$artikel_id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM artikel WHERE artikel_id = $artikel_id");
$data = mysqli_fetch_array($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul'] ?></title>
    <link rel="stylesheet" href="asset/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- ======== Navbar ======== -->
    <!-- <header>
        <nav class="navbar">
            <ul>
                <li><a href="index.php">Beranda</a></li>
                <li><a href="about.html">Tentang</a></li>
                <li><a href="edukasi.php" class="active">Artikel</a></li>
                <li><a href="daftar_jadwal.php">Konseling</a></li>
            </ul>
            <a href="signin.php" class="no-undlin">
                <button class="btn-primary-log">Masuk</button>
            </a>
        </nav>
    </header> -->

    <header class="detail-header">
        <nav class="navbar-simple">
            <a href="edukasi.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </nav>
    </header>

    <!-- ======== Konten Detail Artikel ======== -->
    <main class="artikel-detail">
        <div class="artikel-container">
            <h1><?= $data['judul'] ?></h1>
            <p class="tanggal">Diterbitkan pada: <?= $data['tanggal'] ?></p>
            <div class="isi-artikel">
                <?= $data['isi'] ?>
            </div>
            <!-- <a href="edukasi.php" class="btn-kembali-art">← Kembali ke Daftar Artikel</a> -->
        </div>
    </main>
    <?php $conn->close(); ?>

    <!-- ======== Footer ======== -->
    <footer>
        <p>© 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa</p>
    </footer>

</body>

</html>