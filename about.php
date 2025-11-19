<?php
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tentang Kami - Edukasi Kesehatan Mental</title>
  <link rel="stylesheet" href="asset/css/style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <!-- <script src="assets/js/script.js" defer></script> -->
</head>
<body>

  <!-- ======== Navbar ======== -->
  <header>
    <nav class="navbar">
      <!-- <img src="assets/img/logo.png" alt="Logo" class="logo"> -->
      <ul>
        <li><a href="index.php">Beranda</a></li>
        <li><a href="about.php" class="active">Tentang</a></li>
        <li><a href="edukasi.php">Artikel</a></li>
        <li><a href="daftar_jadwal.php">Konseling</a></li>
      </ul>
      <a href="signin.php" class="no-undlin">
              <button class="btn-primary-log">Masuk</button>
            </a>
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
          <img src="asset/image/Bahlil.png" alt="Foto Nama 1" class="card-img">
          <div class="info-overlay">
            <h3>Nama orang susah</h3>
            <p>Spesialis dalam terapi perilaku kognitif, berpengalaman membantu remaja dan dewasa muda.</p>
          </div>
        </div>

        <div class="card">
          <img src="asset/image/Bahlil.png" alt="Foto Nama 2" class="card-img">
          <div class="info-overlay">
            <h3>Nama Lahar</h3>
            <p>Berkontribusi dalam penyusunan konten edukasi kesehatan mental dan program digital.</p>
          </div>
        </div>

        <div class="card">
          <img src="asset/image/Bahlil.png" alt="Foto Nama 3" class="card-img">
          <div class="info-overlay">
            <h3>Nama 3</h3>
            <p>Bertanggung jawab atas desain, sistem database, serta pengembangan fitur interaktif.</p>
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
