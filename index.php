<?php
session_start();
include 'connection.php';

// Ambil ID pasien jika login
$pasien_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Function untuk mengambil nama pasien dari tabel users (biarkan jika masih dipakai di file lain)
function getNamaPasien($conn, $id)
{
  $q = mysqli_query($conn, "SELECT nama FROM users WHERE user_id = '$id'");
  $d = mysqli_fetch_assoc($q);
  return $d ? $d['nama'] : "Anonim";
}

// Function baru: ambil NAMA + FOTO pasien untuk testimoni
function getPasienInfo($conn, $id)
{
  $q = mysqli_query($conn, "SELECT nama, foto FROM users WHERE user_id = '$id'");
  $d = mysqli_fetch_assoc($q);

  if (!$d) {
    return ['nama' => 'Anonim', 'foto' => 'default.png'];
  }

  if (empty($d['foto'])) {
    $d['foto'] = 'default.png';
  }

  return $d;
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

<?php unset($_SESSION['logout_success']);
endif; ?>

<body>
  <!-- ======== Navbar ======== -->
  <header>
    <nav class="navbar">
      <ul>
        <li><a href="index.php" class="active">Beranda</a></li>
        <li><a href="about.php">Tentang</a></li>
        <li><a href="edukasi.php">Artikel</a></li>
        <?php
        if (isset($_SESSION['role']) && $_SESSION['role'] == 'dokter') {
          echo '<li><a href="dashboard_dokter.php">Dashboard</a></li>';
        } else {
          echo '<li><a href="daftar_jadwal.php">Konseling</a></li>';
          echo '<li><a href="layanan_aduan.php">Layanan aduan</a></li>';
        }
        ?>
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

  <!-- ======== Welcome Section ======== -->
  <?php
  if (isset($_SESSION['role']) && $_SESSION['role'] == 'dokter') { ?>
    <div class="hero">
      <div class="con-hero">
        <h1>Selamat Datang, Dok!</h1>
        <p>Selamat datang di dashboard dokter. Anda bisa mengelola jadwal atau membagikan ilmu kesehatan mental.</p>

        <div style="margin-top: 20px;">
          <a href="tambah_artikel.php">
            <button class="btn-primary" style="background-color: #e67e22;">+ Tulis Artikel Baru</button>
          </a>
        </div>
      </div>
    </div>
  <?php } else { ?>
    <div class="hero">
      <div class="con-hero">
        <h1>Selamat Datang di Edukasi Kesehatan Mental</h1>
        <p>Temukan wawasan, dukungan, dan konseling online untuk membantu kamu menjaga keseimbangan emosi dan mental.</p>
      </div>
    </div>
  <?php
  }
  ?>

  <!-- ======== Testimoni (Dari Database) ======== -->
  <section style="padding: 40px 0;">
    <h2 style="text-align:center; color:#004d47; margin-bottom:20px;">Testimoni Pasien</h2>

    <div class="scroll-container" id="scrollContainer">
      <?php
      if (isset($_SESSION['role']) && $_SESSION['role'] == 'dokter') {
        $dokter_id = $_SESSION['user_id'];
        $query = mysqli_query($conn, "SELECT * FROM rating_testimoni WHERE dokter_id = '$dokter_id' ORDER BY tanggal DESC LIMIT 10");
      } else {
        $query = mysqli_query($conn, "SELECT * FROM rating_testimoni ORDER BY tanggal DESC LIMIT 10");
      }

      while ($data = mysqli_fetch_array($query)) {
        // Ambil nama & foto pasien berdasarkan pasien_id
        $pasienInfo  = getPasienInfo($conn, $data['pasien_id']);
        $namaPasien  = $pasienInfo['nama'];
        $fotoPasien  = $pasienInfo['foto'] ?: 'default.png';
        $fotoProfile = 'asset/image/profil/' . $fotoPasien;
      ?>
        <div class="card-testi">

          <!-- Foto profil pasien -->
          <img src="<?= $fotoProfile ?>" class="testi-img" alt="Foto <?= $namaPasien ?>">

          <!-- Testimoni -->
          <p class="testi-text">
            "<?= $data['testimoni'] ?>"
          </p>

          <!-- Nama Pasien -->
          <span class="testi-name">– <?= $namaPasien ?></span>
        </div>

      <?php } ?>
    </div>
  </section>

  <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'dokter') { ?>
    <!-- ======== Ajakan Konseling ======== -->
    <section class="hero" style="background:linear-gradient(to right,#e0f7f4,#f4fffe); padding:80px 10%;">
      <h2>Ingin melihat dashboard anda ?</h2>
      <p>Anda bisa melihat jadwal, riwayat konseling, dan data layanan anda</p>
      <a href="dashboard_dokter.php"><button class="btn-primary">Dashboard Anda</button></a>
    </section>
  <?php } else { ?>
    <section class="hero" style="background:linear-gradient(to right,#e0f7f4,#f4fffe); padding:80px 10%;">
      <h2>Butuh Seseorang untuk Mendengarkan?</h2>
      <p>Kamu tidak sendiri. Tim konselor kami siap membantu tanpa stigma.</p>
      <a href="daftar_jadwal.php"><button class="btn-primary">Hubungi Konselor</button></a>
    </section>
  <?php } ?>

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

    document.addEventListener("DOMContentLoaded", function() {
      const container = document.querySelector(".scroll-container");
      const cards = container.querySelectorAll(".card-testi");

      if (cards.length < 4) {
        container.classList.add("few-items");
      }
    });
  </script>
</body>

</html>