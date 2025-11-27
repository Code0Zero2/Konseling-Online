<?php
session_start();
include 'connection.php';

// Cek login
if (!isset($_SESSION['user_id']) && !isset($_SESSION['id_pasien'])) {
    header("Location: signin.php");
    exit;
}

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : $_SESSION['id_pasien'];

// Ambil data user saat ini
$sql  = "SELECT * FROM users WHERE user_id = '$user_id'";
$query = mysqli_query($conn, $sql);
$user  = mysqli_fetch_assoc($query);

if (!$user) {
    die("Data user tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama   = mysqli_real_escape_string($conn, $_POST['nama']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp  = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    $err_msg   = "";
    $foto_lama = $user['foto'] ?: 'default.png';
    $foto_baru = $foto_lama;

    // Jika user upload foto baru
    if (!empty($_FILES['foto']['name'])) {
        $file_name = $_FILES['foto']['name'];
        $file_tmp  = $_FILES['foto']['tmp_name'];
        $file_err  = $_FILES['foto']['error'];

        if ($file_err === 0) {
            $allowed_ext = ['jpg','jpeg','png','gif'];
            $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (in_array($ext, $allowed_ext)) {
                $new_name   = 'user_' . $user_id . '_' . time() . '.' . $ext;
                $upload_dir = 'asset/image/profil/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                    if ($foto_lama !== 'default.png' && file_exists($upload_dir . $foto_lama)) {
                        @unlink($upload_dir . $foto_lama);
                    }
                    $foto_baru = $new_name;
                } else {
                    $err_msg = "Gagal mengupload foto.";
                }
            } else {
                $err_msg = "Format foto tidak didukung. Gunakan JPG, JPEG, PNG, atau GIF.";
            }
        } else {
            $err_msg = "Terjadi kesalahan saat upload foto.";
        }
    }

    if ($err_msg === "") {
        $update = "UPDATE users 
                   SET nama='$nama', email='$email', no_hp='$no_hp', alamat='$alamat', foto='$foto_baru'
                   WHERE user_id='$user_id'";
        if (mysqli_query($conn, $update)) {
            $_SESSION['profil_success'] = "Profil berhasil diperbarui.";
        } else {
            $_SESSION['profil_error'] = "Gagal menyimpan perubahan: " . mysqli_error($conn);
        }
    } else {
        $_SESSION['profil_error'] = $err_msg;
    }

    // Selalu kembali ke profil setelah submit
    header("Location: profil.php");
    exit;
}

// Siapkan nilai tampilan (GET)
$nama   = $user['nama'] ?? '';
$email  = $user['email'] ?? '';
$no_hp  = $user['no_hp'] ?? '';
$alamat = $user['alamat'] ?? '';
$foto   = $user['foto'] ?: 'default.png';
$foto_path = 'asset/image/profil/' . $foto;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Profil</title>
    <link rel="stylesheet" href="asset/css/style.css">
    <style>
        .edit-wrapper {
            max-width: 900px;
            margin: 40px auto 60px;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        .edit-photo {
            flex: 0 0 200px;
            text-align: center;
        }
        .edit-photo img {
            width: 170px;
            height: 170px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #008f85;
        }
        .edit-photo p {
            margin-top: 10px;
            font-size: 0.9rem;
            color: #666;
        }
        .edit-form {
            flex: 1;
        }
        .edit-form .form-group {
            margin-bottom: 15px;
        }
        .edit-form label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #004d47;
        }
        .edit-form input,
        .edit-form textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-family: inherit;
            font-size: 0.95rem;
        }
        .edit-form textarea {
            resize: vertical;
            min-height: 80px;
        }
        .btn-save {
            background: #008f85;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 0.95rem;
            cursor: pointer;
            margin-right: 10px;
            transition: 0.3s;
        }
        .btn-save:hover {
            background: #007970;
        }
        .btn-cancel {
            display: inline-block;
            padding: 9px 18px;
            border-radius: 8px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #555;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

<main>
    <section style="padding: 40px 10%;">

        <h1 style="text-align:center; color:#004d47; margin-bottom:10px;">Edit Profil</h1>
        <p style="text-align:center; color:#666; margin-bottom:20px;">
            Perbarui informasi dan foto profil Anda.
        </p>

        <div class="edit-wrapper">
            <div class="edit-photo">
                <img src="<?= $foto_path ?>" alt="Foto Profil">
                <p>Foto Profil Anda</p>
            </div>

            <div class="edit-form">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" value="<?= $nama ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= $email ?>" required>
                    </div>

                    <div class="form-group">
                        <label>No. HP</label>
                        <input type="text" name="no_hp" value="<?= $no_hp ?>">
                    </div>

                    <div class="form-group">
                        <label>Alamat</label>
                        <textarea name="alamat"><?= $alamat ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Foto Profil (opsional)</label>
                        <input type="file" name="foto" accept="image/*">
                    </div>

                    <button type="submit" class="btn-save">Simpan Perubahan</button>
                    <a href="profil.php" class="btn-cancel">Batal</a>
                </form>
            </div>
        </div>

    </section>
</main>

</body>
</html>
