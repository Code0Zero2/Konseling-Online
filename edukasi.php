<?php
session_start();
include 'connection.php';

$link_beranda = 'index.php';

if(isset($_SESSION['id_pasien'])){
  $id_user = $_SESSION['id_pasien'];

  $cek = mysqli_query($conn, "SELECT * FROM users WHERE user_id = '$id_user'"); // ambil role user buat cek role
  $data_role = mysqli_fetch_assoc($cek);

  if($data_role && $data_role['role'] == 'dokter'){
    $link_beranda = 'dashboard_dokter.php';
  }
}

$pasien_id = isset($_SESSION['id_pasien']) ? $_SESSION['id_pasien'] : null;
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
      <!-- <img src="assets/img/logo.png" alt="Logo" class="logo"> -->
      <ul>
        <li><a href="<?= $link_beranda ?>">Beranda</a></li>
        <li><a href="about.php">Tentang</a></li>
        <li><a href="edukasi.php" class="active">Artikel</a></li>
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

  <!-- ======== Konten Utama ======== -->
  <main class="edukasi">
    <section class="artikel-section">
      <h1>Artikel Edukasi Kesehatan Mental</h1>

      <?php
      include 'connection.php';
      $query = mysqli_query($conn, "SELECT * FROM artikel ORDER BY tanggal DESC");
      if ($query->num_rows > 0) {
        while ($row = mysqli_fetch_array($query)) {
      ?>
              <div class='artikel-card'>
                <h2><?= $row['judul'] ?></h2>
                <p class='tanggal'>Diterbitkan pada: <?= $row['tanggal'] ?></p>
                <p><?= substr($row['isi'], 0, 100) ?>...</p>
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