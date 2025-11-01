<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}
include '../koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link rel="stylesheet" href="style_admin.css">
</head>
<body>
  <div class="admin-container">
    <h1>Dashboard Admin</h1>
    <p>Selamat datang, <?php echo $_SESSION['admin']; ?>!</p>
    <nav>
      <a href="tambah_artikel.php">Tambah Artikel</a>
      <a href="../index.php">Lihat Website</a>
      <a href="logout.php">Logout</a>
    </nav>

    <h2>Daftar Pesan Konseling</h2>
    <table>
      <tr>
        <th>Nama</th>
        <th>Email</th>
        <th>Pesan</th>
        <th>Tanggal</th>
      </tr>

      <?php
      $sql = "SELECT * FROM konseling ORDER BY tanggal DESC";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
          echo "<tr>
                  <td>{$row['nama']}</td>
                  <td>{$row['email']}</td>
                  <td>{$row['pesan']}</td>
                  <td>{$row['tanggal']}</td>
                </tr>";
        }
      } else {
        echo "<tr><td colspan='4'>Belum ada pesan masuk.</td></tr>";
      }
      ?>
    </table>
  </div>
</body>
</html>
