<?php
session_start();
include 'connection.php';

// Ambil ID pasien jika login
$pasien_id = isset($_SESSION['id_pasien']) ? $_SESSION['id_pasien'] : null;

// Function untuk mengambil nama pasien dari tabel users
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
  <title>Beranda - Edukasi Kesehatan Mental</title>
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
        <li><a href="index.php" class="active">Beranda</a></li>
        <li><a href="about.php">Tentang</a></li>
        <li><a href="edukasi.php">Artikel</a></li>
        <li><a href="daftar_jadwal.php">Konseling</a></li>
      </ul>
      <?php if (isset($_SESSION['id_pasien'])): ?>
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
    <h1>Selamat Datang di Edukasi Kesehatan Mental</h1>
    <p>Temukan wawasan, dukungan, dan konseling online untuk membantu kamu menjaga keseimbangan emosi dan mental.</p>
  </div>

  <!-- ======== Testimoni (Dari Database) ======== -->
  <section style="padding: 40px 0;">
    <h2 style="text-align:center; color:#004d47; margin-bottom:20px;">Apa Kata Mereka?</h2>

    <div class="scroll-container" id="scrollContainer">
      <?php
      $query = mysqli_query($conn, "SELECT * FROM rating_testimoni ORDER BY tanggal DESC");
      while ($data = mysqli_fetch_array($query)) {

        // Ambil nama pasien berdasarkan pasien_id
        $namaPasien = getNamaPasien($conn, $data['pasien_id']);
      ?>
        <div class="card-testi">

  <!-- Gambar kecil testimoni -->
  <img src="asset/image/depresi.jpeg" class="testi-img" alt="Ilustrasi">

  <!-- Testimoni -->
  <p class="testi-text">
    "<?= htmlspecialchars($data['testimoni']) ?>"
  </p>

  <!-- Nama Pasien -->
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
    });
  </script>
</body>

</html>
