<?php 
include 'connection.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];
$nohp = $_POST['nohp'];

// CEK EMAIL SUDAH ADA
$cekEmail = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
if (mysqli_num_rows($cekEmail) > 0) {

    // Output HTML lengkap agar SweetAlert2 bisa tampil sempurna
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Email sudah digunakan',
                text: 'Silakan gunakan email lain.',
            }).then(() => {
                window.location.href = 'signup.php';
            });
        </script>
    </body>
    </html>";
    exit;
}

// INSERT DATA
$query = mysqli_query($conn, "INSERT INTO users(nama, email, password, role, no_hp)
                              VALUES ('$nama','$email','$password','$role','$nohp')");

if ($query) {

    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Akun Anda berhasil dibuat.',
            }).then(() => {
                window.location.href = 'signin.php';
            });
        </script>
    </body>
    </html>";
    exit;

} else {
    echo mysqli_error($conn);
}
?>
