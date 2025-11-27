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

<!-- dashboard dokter old -->
<?php
session_start();
include 'connection.php';

// Ambil id user dari session
$user_id = $_SESSION['id_pasien'];

// Cek role user (harus dokter)
$cek = mysqli_query($conn, "SELECT role FROM users WHERE user_id = '$user_id'");
$dataRole = mysqli_fetch_assoc($cek);

if ($dataRole['role'] != 'dokter') {
    die("Anda bukan dokter!");
}

// Fungsi untuk ambil nama pasien dari tabel users
function getNamaPasien($conn, $id) {
    $q = mysqli_query($conn, "SELECT nama FROM users WHERE user_id = '$id'");
    $d = mysqli_fetch_assoc($q);
    return $d ? $d['nama'] : "Anonim";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Beranda Dokter - Edukasi Kesehatan Mental</title>
  <link rel="stylesheet" href="asset/css/style.css">
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

<?php unset($_SESSION['logout_success']); endif; ?>

<body>
  <!-- ======== Navbar ======== -->
  <header>
    <nav class="navbar">
      <ul>
        <li><a href="dashboard_dokter.php" class="active">Beranda</a></li>
        <li><a href="about.php">Tentang</a></li>
        <li><a href="edukasi.php">Artikel</a></li>
        <li><a href="daftar_jadwal.php">Konseling</a></li>
      </ul>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="logout.php" class="no-undlin">
          <button class="btn-primary-log">Logout</button>
        </a>
      <?php else: ?>
        <a href="signin.php" class="no-undlin">
          <button class="btn-primary-log">Masuk</button>
        </a>
      <?php endif; ?>
    </nav>
  </header>

  <!-- ======== Welcome Section ======== -->
  <div class="hero">
    <h1>Selamat Datang, Dok!</h1>
    <p>Selamat datang di dashboard dokter. Anda bisa mengelola jadwal atau membagikan ilmu kesehatan mental.</p>
    
    <div style="margin-top: 20px;">
        <a href="tambah_artikel.php">
            <button class="btn-primary" style="background-color: #e67e22;">+ Tulis Artikel Baru</button>
        </a>
    </div>
  </div>

  <!-- ======== Testimoni Pasien ======== -->
  <section style="padding: 40px 0;">
    <h2 style="text-align:center; color:#004d47; margin-bottom:20px;">
      Apa Kata Pasien Anda?
    </h2>

    <div class="scroll-container" id="scrollContainer">
      <?php
      // Ambil semua rating & testimoni, terbaru dulu
      $queryTesti = mysqli_query($conn, "SELECT * FROM rating_testimoni ORDER BY tanggal DESC");
      while ($rowTesti = mysqli_fetch_array($queryTesti)) {

        // Ambil nama pasien berdasarkan pasien_id
        $namaPasien = getNamaPasien($conn, $rowTesti['pasien_id']);
      ?>
        <div class="card-testi">
          <!-- Gambar ilustrasi -->
          <img src="asset/image/depresi.jpeg" class="testi-img" alt="Ilustrasi">

          <!-- Isi testimoni -->
          <p class="testi-text">
            "<?= htmlspecialchars($rowTesti['testimoni']) ?>"
          </p>

          <!-- Nama pasien -->
          <span class="testi-name">– <?= htmlspecialchars($namaPasien) ?></span>
        </div>
      <?php } ?>
    </div>
  </section>

  <!-- ======== Ajakan Konseling ======== -->
  <section class="hero" style="background:linear-gradient(to right,#e0f7f4,#f4fffe); padding:80px 10%;">
    <h2>Butuh Seseorang untuk Mendengarkan?</h2>
    <p>Kamu tidak sendiri. Tim konselor kami siap membantu tanpa stigma.</p>
    <a href="daftar_jadwal.php"><button class="btn-primary">Hubungi Konselor</button></a>
  </section>

  <!-- ======== Footer ======== -->
  <footer>
    <p>© 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa</p>
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const scrollContainer = document.getElementById("scrollContainer");

      let isDown = false;
      let startX;
      let scrollLeft;

      scrollContainer.addEventListener("mousedown", (e) => {
        isDown = true;
        startX = e.pageX - scrollContainer.offsetLeft;
        scrollLeft = scrollContainer.scrollLeft;
      });

      scrollContainer.addEventListener("mouseleave", () => {
        isDown = false;
      });

      scrollContainer.addEventListener("mouseup", () => {
        isDown = false;
      });

      scrollContainer.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - scrollContainer.offsetLeft;
        const walk = (x - startX) * 1.5;
        scrollContainer.scrollLeft = scrollLeft - walk;
      });

      // Scroll dengan drag terasa halus, wheel pakai default browser
    });
  </script>
</body>

</html>


<!-- dari booking.php -->
 // Cek apakah jadwal sudah ada yang booking
$cek_slot = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total FROM booking 
    WHERE jadwal_id='$jadwal_id' AND status IN ('menunggu','disetujui')
"));

if ($cek_slot['total'] >= 1) {
    echo "<script>alert('Jadwal sudah penuh!'); window.location='daftar_jadwal.php';</script>";
    exit;
}


// Cek apakah pasien sudah pernah booking jadwal yg sama
$cek_booking = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT * FROM booking 
    WHERE pasien_id='$pasien_id' AND jadwal_id='$jadwal_id'
"));

if ($cek_booking) {
    echo "<script>alert('Anda sudah pernah booking jadwal ini!'); window.location='daftar_jadwal.php';</script>";
    exit;
}

// --- Jika tombol submit ditekan ---
if (isset($_POST['submit'])) {

    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $dokter_id = $jadwal['dokter_id'];

    // Simpan booking baru
    $insert = mysqli_query($conn, "
        INSERT INTO booking (pasien_id, dokter_id, jadwal_id, status, catatan)
        VALUES ('$pasien_id', '$dokter_id', '$jadwal_id', 'menunggu', '$catatan')
    ");

    if ($insert) {
        echo "<script>alert('Booking berhasil! Menunggu persetujuan dokter.'); window.location='daftar_jadwal.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal booking, coba lagi.');</script>";
    }
}