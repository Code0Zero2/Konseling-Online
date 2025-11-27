<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="asset/css/LogReg.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <form action="signin_proses.php" method="POST">
            <label>Email</label>
            <input type="email" name="email" placeholder="Masukkan email" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>
            <button type="submit">Login</button>
        </form>
        <div class="footer">
            Belum punya akun? <a href="signup.php">Daftar</a> | <a style="color: rgba(0, 47, 255, 0.84);" href="index.php">Kembali</a>
        </div>
    </div>
</body>
</html>
