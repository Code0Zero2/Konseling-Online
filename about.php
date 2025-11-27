<?php
session_start();
include 'connection.php';

$link_beranda = 'index.php';

if(isset($_SESSION['user_id'])){
  $id_user = $_SESSION['user_id'];

  $cek = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"); // ambil role user buat cek role
  $data_role = mysqli_fetch_assoc($cek);

  if($data_role && $data_role['role'] == 'dokter'){
    $link_beranda = 'dashboard_dokter.php';
  }
}

// Ambil ID pasien jika login
$pasien_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang Kami - Edukasi Kesehatan Mental</title>
  <link rel="stylesheet" href="asset/css/style.css">
  <!-- <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet"> -->
  <!-- <script src="assets/js/script.js" defer></script> -->
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
      <!-- <img src="assets/img/logo.png" alt="Logo" class="logo"> -->
      <ul>
        <li><a href="index.php">Beranda</a></li>
        <li><a href="about.php" class="active">Tentang</a></li>
        <li><a href="edukasi.php">Artikel</a></li>
        <?php 
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'dokter') {
          echo '<li><a href="dashboard_dokter.php">Dashboard</a></li>';
        } else {
          echo '<li><a href="daftar_jadwal.php">Konseling</a></li>';
        }
        ?>
        <!-- <li><a href="daftar_jadwal.php">Konseling</a></li> -->
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

  <!-- ======== Konten Utama ======== -->
  <main class="about">
    <section class="about-section">
      <h1>Tentang Kami</h1>
      <p>
        Website <strong>Edukasi Kesehatan Mental</strong> hadir untuk membantu masyarakat memahami pentingnya kesehatan mental.
        Kami berkomitmen menyediakan informasi, edukasi, serta layanan konseling daring yang dapat diakses siapa pun.
      </p>
      <p>
        Kami percaya bahwa setiap orang berhak untuk merasa baik, mendapatkan dukungan emosional, 
        dan belajar mengelola stres dengan cara yang sehat. Melalui platform ini, kami menghubungkan pengguna 
        dengan konselor profesional yang siap mendengarkan dan membantu tanpa stigma.
      </p>
    </section>

<section class="team-section">
      <h2>Tim Kami</h2>
      <div class="card-container">
        <div class="card">
          <img src="asset/image/DOKTER.jpg" alt="Foto Nama 1" class="card-img">
          <div class="info-overlay">
             <h3>Ryan Haqqi Prarista</h3>
             <h5>123240067</h5>
    <p>Berpengalaman menangani gangguan kecemasan, stres kerja, serta terapi perilaku kognitif untuk remaja dan dewasa.</p>
  </div>
</div>

        <div class="card">
          <img src="asset/image/AJRUN.png" alt="Foto Nama 2" class="card-img">
          <div class="info-overlay">
             <h3>dr. Bima Sakti, M.Psi.</h3>
    <p>Fokus pada konseling pengelolaan emosi, mindfulness, serta pendampingan klien dengan burnout dan masalah hubungan interpersonal.</p>
  </div>
</div>

        <div class="card">
          <img src="asset/image/AJRUN2.jpg" alt="Foto Nama 3" class="card-img">
          <div class="info-overlay">
             <h3>dr. Raditya Ananda, Sp.KJ</h3>
    <p>Ahli dalam menangani depresi jangka panjang, terapi keluarga, dan pemulihan kesehatan mental dalam lingkungan kerja.</p>
  </div>
</div>
      </div>
    </section>
  </main>

  <!-- ======== Footer ======== -->
  <footer>
    <p>© 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa</p>
  </footer>

</body>
</html>
