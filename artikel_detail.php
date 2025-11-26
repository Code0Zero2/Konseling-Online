<?php
session_start();
include 'connection.php';

$link_beranda = 'index.php'; 
if(isset($_SESSION['id_pasien'])){
    $id_user = $_SESSION['id_pasien'];
    $cek = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'");
    $data_role = mysqli_fetch_assoc($cek);

    if($data_role && $data_role['role'] == 'dokter'){
        $link_beranda = 'dashboard_dokter.php';
    }
}

if(isset($_GET['id'])){
    $id = $_GET['id'];

    $sql = "SELECT * FROM artikel WHERE artikel_id = '$id'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    } else{
        echo "<script>alert('Artikel tidak ditemukan!'); window.location='edukasi.php';</script>";
        exit;
    }

} else{
    header("Location: edukasi.php");
    exit;
}

/* =====================================================
   FUNGSI AUTO-LINK
   ===================================================== */
function autoLink($text) {
    // Deteksi URL
    $pattern = '/(https?:\/\/[^\s]+)/i';

    // Jadikan hyperlink
    $replace = '<a href="$1" target="_blank" style="color:#0066cc; text-decoration:underline;">$1</a>';

    return preg_replace($pattern, $replace, $text);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $row['judul'] ?></title>
    <link rel="stylesheet" href="asset/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <!-- ======== Navbar ======== -->
    <header class="detail-header">
        <nav class="navbar-simple">
            <a href="edukasi.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="<?= $link_beranda ?>" class="back-button" style="margin-left: 10px;">
                <span>Beranda</span> 
            </a>
        </nav>
    </header>

    <!-- ======== Konten Detail Artikel ======== -->
    <main class="artikel-detail">
        <div class="artikel-container">
            <h1><?= $row['judul'] ?></h1>
            <p class="tanggal">Diterbitkan pada: <?= $row['tanggal'] ?></p>

            <div class="isi-artikel">
                <?= autoLink($row['isi']) ?>
            </div>

        </div>
    </main>

    <?php $conn->close(); ?>

    <!-- ======== Footer ======== -->
    <footer>
        <p>© 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa</p>
    </footer>

</body>

</html>
