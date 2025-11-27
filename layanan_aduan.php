<?php
session_start();
include 'connection.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama     = $_POST['nama'] ?? null;
    $email    = $_POST['email'] ?? null;
    $kategori = $_POST['kategori'];
    $isi      = $_POST['isi'];

    if (empty($isi) || empty($kategori)) {
        $error = "Kategori dan isi aduan wajib diisi!";
    } else {
        $stmt = $conn->prepare("INSERT INTO layanan_aduan (nama, email, kategori, isi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $kategori, $isi);

        if ($stmt->execute()) {
            $success = "Aduan berhasil dikirim!";
        } else {
            $error = "Gagal menyimpan aduan: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Layanan Aduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    /* HEADER */
    .custom-header {
        background: #e0f7f4;
        padding: 25px 0;
        margin-bottom: 30px;
    }
    .custom-header h2 {
        color: #004d47;
        font-weight: 700;
        text-align: center;
    }

    /* CARD FORM */
    .aduan-card {
        border: none;
        border-radius: 14px;
        box-shadow: 0 8px 26px rgba(0,0,0,0.12);
        padding: 35px;
        background: white;
    }

    label {
        font-weight: 600;
        color: #004d47;
    }

    .btn-primary {
        background: #008f85;
        border: none;
    }

    .btn-primary:hover {
        background: #007970;
    }

    /* FOOTER */
    footer {
        background: #e0f7f4;
        color: #004d47;
        text-align: center;
        padding: 18px;
        margin-top: 60px;
        font-weight: 500;
    }

    /* FLOATING WHATSAPP BUTTON */
    .wa-floating {
        position: fixed;
        bottom: 25px;
        right: 25px;
        width: 60px;
        height: 60px;
        background: #25D366;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.25);
        cursor: pointer;
        z-index: 9999;
        transition: 0.25s;
    }

    .wa-floating:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 16px rgba(0,0,0,0.35);
    }

    .wa-floating img {
        width: 34px;
        height: 34px;
        object-fit: contain;
        filter: brightness(0) invert(1);
    }
</style>
</head>

<body class="bg-light">

<!-- HEADER -->
<div class="custom-header">
    <h2>Layanan Aduan & Saran</h2>
</div>

<div class="container mb-5" style="max-width: 650px;">
    
    <a href="about.php" class="btn btn-secondary mb-3">← Kembali ke Tentang</a>

    <div class="aduan-card">

        <?php if ($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="mb-3">
                <label>Nama (opsional)</label>
                <input type="text" name="nama" class="form-control" placeholder="Nama kamu...">
            </div>

            <div class="mb-3">
                <label>Email (opsional)</label>
                <input type="email" name="email" class="form-control" placeholder="Email kamu...">
            </div>

            <div class="mb-3">
                <label>Kategori Aduan *</label>
                <select name="kategori" class="form-select" required>
                    <option value="">Pilih kategori</option>
                    <option>Masalah Sistem</option>
                    <option>Keluhan Konselor</option>
                    <option>Kendala Login</option>
                    <option>Saran Fitur Baru</option>
                    <option>Lainnya</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Isi Aduan *</label>
                <textarea name="isi" class="form-control" rows="5" placeholder="Tuliskan aduan kamu..." required></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">Kirim Aduan</button>

        </form>
    </div>
</div>

<!-- FOOTER -->
<footer>
    © 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa
</footer>

<!-- Floating WhatsApp Button -->
<a href="https://wa.me/6285640346523" target="_blank" class="wa-floating">
    <img src="asset/image/WA.png" alt="WhatsApp">
</a>

</body>
</html>
