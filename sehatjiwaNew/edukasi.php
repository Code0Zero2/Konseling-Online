<?php
// Koneksi ke database
include 'connection.php';

// Ambil data artikel dari tabel 'artikel'
$sql = "SELECT * FROM artikel ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edukasi - Kesehatan Mental</title>
  <link rel="stylesheet" href="asset/css/style.css">
  <!-- <script src="assets/js/script.js" defer></script> -->
</head>
<body>

  <!-- ======== Navbar ======== -->
  <header>
    <nav class="navbar">
      <img src="assets/img/logo.png" alt="Logo" class="logo">
      <ul>
        <li><a href="index.php">Beranda</a></li>
        <li><a href="about.html">Tentang</a></li>
        <li><a href="edukasi.php" class="active">Artikel</a></li>
        <li><a href="daftar_jadwal copy.php">Konseling</a></li>
      </ul>
    </nav>
  </header>

  <!-- ======== Konten Utama ======== -->
  <main class="edukasi">
    <section class="artikel-section">
      <h1>Artikel Edukasi Kesehatan Mental</h1>

      <?php
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "
              <div class='artikel-card'>
                <h2>{$row['judul']}</h2>
                <p class='tanggal'>Diterbitkan pada: {$row['tanggal']}</p>
                <p>" . nl2br(substr($row['isi'], 0, 300)) . "...</p>
                <a href='artikel_detail.php?id={$row['artikel_id']}' class='btn-baca'>Baca Selengkapnya</a>
              </div>
              ";
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
