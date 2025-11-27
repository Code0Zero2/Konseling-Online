<?php
session_start();
include 'connection.php';

$link_beranda = 'index.php';

if(isset($_SESSION['user_id'])){
  $id_user = $_SESSION['user_id'];

  $cek = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"); 
  $data_role = mysqli_fetch_assoc($cek);

  if($data_role && $data_role['role'] == 'dokter'){
    $link_beranda = 'dashboard_dokter.php';
  }
}

$pasien_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

/* =====================================================
   FUNGSI AUTO-LINK
   ===================================================== */
function autoLink($text) {
    $pattern = '/(https?:\/\/[^\s]+)/i';
    $replace = '<a href="$1" target="_blank" style="color:#0066cc; text-decoration:underline;">$1</a>';
    return preg_replace($pattern, $replace, $text);
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edukasi - Kesehatan Mental</title>
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
        <li><a href="index.php">Beranda</a></li>
        <li><a href="about.php">Tentang</a></li>
        <li><a href="edukasi.php" class="active">Artikel</a></li>
        <?php 
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'dokter') {
          echo '<li><a href="dashboard_dokter.php">Dashboard</a></li>';
        } else {
          echo '<li><a href="daftar_jadwal.php">Konseling</a></li>';
          echo '<li><a href="layanan_aduan.php">Layanan aduan</a></li>';
        }
        ?>
        <!-- <li><a href="daftar_jadwal.php">Konseling</a></li> -->
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

  <!-- ======== Konten Utama ======== -->
  <main class="edukasi">
    <section class="artikel-section">
      <h1>Artikel Edukasi Kesehatan Mental</h1>

      <?php
      $query = mysqli_query($conn, "SELECT * FROM artikel ORDER BY tanggal DESC");
      if ($query->num_rows > 0) {
        while ($row = mysqli_fetch_array($query)) {

          // Ambil 100 karakter isi artikel untuk preview
          $preview = substr($row['isi'], 0, 100) . '...';
          // Convert auto hyperlink
          $preview = autoLink($preview);
      ?>
              <div class='artikel-card'>
                <h2><?= $row['judul'] ?></h2>
                <p class='tanggal'>Diterbitkan pada: <?= $row['tanggal'] ?></p>

                <!-- Isi preview link aktif -->
                <p><?= $preview ?></p>

                <a href='artikel_detail.php?id=<?= $row['artikel_id'] ?>' class='btn-baca'>Baca Selengkapnya</a>
              </div>
      <?php
        }
      } else {
        echo "<p>Tidak ada artikel yang tersedia saat ini.</p>";
      }

      $conn->close();
      ?>
    </section>
  </main>

  <!-- ======== Footer ======== -->
  <footer>
    <p>© 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa</p>
  </footer>

</body>

</html>
