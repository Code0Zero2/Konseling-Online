<?php
session_start();
include 'connection.php';

// Cek apakah user sudah login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_pasien'])) {
    header("Location: signin.php");
    exit;
}

// Samakan ID user (baik pasien maupun dokter)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id_pasien'];

// Ambil data user dari database
$sql  = "SELECT * FROM users WHERE user_id = '$user_id'";
$query = mysqli_query($conn, $sql);
$user  = mysqli_fetch_assoc($query);

if (!$user) {
    die("Data user tidak ditemukan.");
}

// Siapkan nilai untuk ditampilkan
$nama   = $user['nama'] ?? '-';
$email  = $user['email'] ?? '-';
$role   = $user['role'] ?? '-';
$no_hp  = $user['no_hp'] ?? '-';
$alamat = $user['alamat'] ?? '-';
$foto   = $user['foto'] ?: 'default.png';

// Path gambar profil
$foto_path = 'asset/image/profil/' . $foto;

// Tentukan link beranda sesuai role
$link_beranda = 'index.php';
if ($role == 'dokter') {
    $link_beranda = 'dashboard_dokter.php';
}

// Ambil pesan dari edit_profil (jika ada)
$alert_success = $_SESSION['profil_success'] ?? '';
$alert_error   = $_SESSION['profil_error'] ?? '';
unset($_SESSION['profil_success'], $_SESSION['profil_error']);

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        .profile-wrapper {
            max-width: 900px;
            margin: 40px auto 60px;
            padding: 30px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .profile-photo {
            flex: 0 0 180px;
            text-align: center;
        }

        .profile-photo img {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #008f85;
        }

        .profile-photo p {
            margin-top: 10px;
            font-size: 0.95rem;
            color: #555;
        }

        .profile-info {
            flex: 1;
        }

        .profile-info h2 {
            margin-bottom: 5px;
            color: #004d47;
        }

        .profile-info .role-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            background: #e0f7f4;
            color: #006d67;
            margin-bottom: 15px;
        }

        .profile-row {
            margin-bottom: 10px;
        }

        .profile-row span.label {
            display: inline-block;
            width: 90px;
            font-weight: 600;
            color: #555;
        }

        .profile-actions {
            margin-top: 25px;
        }

        .btn-edit {
            display: inline-block;
            background: #008f85;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.95rem;
            transition: 0.3s;
        }

        .btn-edit:hover {
            background: #007970;
        }

        .btn-logout {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.95rem;
            margin-left: 10px;
            transition: 0.3s;
        }

        .btn-logout:hover {
            background: #c0392b;
        }

        .alert-success,
        .alert-error {
            max-width: 900px;
            margin: 20px auto -10px;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .alert-success {
            background: #e6ffef;
            color: #0c6b3f;
            border: 1px solid #a6e4bf;
        }

        .alert-error {
            background: #ffecec;
            color: #b00020;
            border: 1px solid #f3b5b5;
        }
    </style>
</head>

<body>

    <!-- ======== Header Kembali ======== -->
    <header style="background-color:#e0f7f4; padding:15px 10%;">
        <a href="<?= $link_beranda ?>" style="color:#006d67; text-decoration:none; font-weight:600;">
            ← Kembali
        </a>
    </header>

    <!-- ======== Konten Profil ======== -->
    <main>
        <section style="padding: 40px 10%;">
            <h1 style="text-align:center; color:#004d47; margin-bottom:25px;">Profil Saya</h1>

            <?php if ($alert_success): ?>
                <div class="alert-success"><?= $alert_success ?></div>
            <?php endif; ?>

            <?php if ($alert_error): ?>
                <div class="alert-error"><?= $alert_error ?></div>
            <?php endif; ?>

            <div class="profile-wrapper">
                <div class="profile-photo">
                    <img src="<?= $foto_path ?>" alt="Foto Profil">
                    <p>Foto profil Anda</p>
                </div>

                <div class="profile-info">
                    <h2><?= $nama ?></h2>
                    <div class="role-badge">
                        <?= $role == 'dokter' ? 'Dokter' : 'Pasien'; ?>
                    </div>

                    <div class="profile-row">
                        <span class="label">Email</span>:
                        <span><?= $email ?></span>
                    </div>
                    <div class="profile-row">
                        <span class="label">No. HP</span>:
                        <span><?= $no_hp ?></span>
                    </div>
                    <div class="profile-row">
                        <span class="label">Alamat</span>:
                        <span><?= $alamat ?></span>
                    </div>

                    <div class="profile-actions">
                        <a href="edit_profil.php" class="btn-edit">Edit Profil</a>
                        <a href="logout.php" class="btn-logout">Logout</a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ======== Footer ========
    <footer>
        <p>© 2025 Edukasi Kesehatan Mental | Bersama untuk Indonesia Sehat Jiwa</p>
    </footer> -->

</body>

</html>