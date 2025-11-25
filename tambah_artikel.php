<?php
session_start();
include 'connection.php';


if (!isset($_SESSION['id_pasien'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['id_pasien'];
$cek_dokter = mysqli_query($conn, "SELECT role FROM users WHERE user_id = '$user_id'");
$data_user = mysqli_fetch_assoc($cek_dokter);

if (!$data_user || $data_user['role'] !== 'dokter') {
    echo "<script>alert('Akses ditolak! Anda bukan dokter.'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['simpan'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $isi   = mysqli_real_escape_string($conn, $_POST['isi']);
    $tanggal = date('Y-m-d H:i:s'); // Format datetime sesuai database

    // Insert data ke tabel artikel
    $query_simpan = "INSERT INTO artikel (dokter_id, judul, isi, tanggal) 
                     VALUES ('$user_id', '$judul', '$isi', '$tanggal')";

    if (mysqli_query($conn, $query_simpan)) {
        echo "<script>alert('Artikel berhasil diterbitkan!'); window.location='edukasi.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Artikel - Dokter</title>
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        /* Styling khusus form agar rapi di tengah */
        body { background-color: #f9fcfc; }
        .form-container {
            max-width: 800px;
            margin: 50px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #004d47; }
        .form-group input, .form-group textarea {
            width: 100%; padding: 12px; border: 1px solid #ccc; border-radius: 8px; font-family: inherit;
        }
        .btn-submit {
            background-color: #008f85; color: white; padding: 12px 24px; border: none;
            border-radius: 8px; cursor: pointer; font-size: 16px; width: 100%; font-weight: bold;
        }
        .btn-submit:hover { background-color: #007970; }
        .btn-batal {
            display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none;
        }
    </style>
</head>
<body>

    <header>
        <nav class="navbar">
            <ul>
                <li><a href="dashboard_dokter.php">Dashboard</a></li>
                <li><a href="#" class="active">Tulis Artikel</a></li>
            </ul>
        </nav>
    </header>

    <div class="form-container">
        <h2 style="text-align:center; color:#004d47; margin-bottom:30px;">Tulis Artikel Baru</h2>
        
        <form action="input_proses.php" method="POST">
            <div class="form-group">
                <label>Judul Artikel</label>
                <input type="text" name="judul" placeholder="Contoh: Tips Mengatasi Insomnia" required>
            </div>

            <div class="form-group">
                <label>Isi Artikel</label>
                <textarea name="isi" rows="12" placeholder="Tuliskan isi artikel edukasi di sini..." required></textarea>
            </div>

            <button type="submit" name="simpan" class="btn-submit">Terbitkan Artikel</button>
            <a href="dashboard_dokter.php" class="btn-batal">Batal</a>
        </form>
    </div>

</body>
</html>