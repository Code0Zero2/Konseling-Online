<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="asset/css/LogReg.css">
    <title>Sign Up</title>
</head>
<body>
    <div class="container">
        <h2>Daftar Akun Baru</h2>
        <form action="signup_proses.php" method="POST">    
            <label>Nama Lengkap</label>
            <input type="text" name="nama" placeholder="Masukkan nama lengkap" required>
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            <label>Pasien/Dokter</label>
            <select name="role" id="role" require>
                <option value="" >--Pilih--</option>
                <option value="pasien">Pasien</option>
                <option value="dokter">Dokter</option>
            </select>
            <label>Nomor Hp</label>
            <input type="text" name="nohp" placeholder="Masukkan nomor hp (+62)" required>
            <button type="submit">Daftar Sekarang</button>
        </form>
        <div class="footer">
            Sudah punya akun? <a href="signin.php">Masuk</a>
        </div>
    </div>
</body>
</html>
