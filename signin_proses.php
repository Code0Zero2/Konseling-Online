<?php 
include 'connection.php';
session_start();
$email = $_POST['email'];
$password = $_POST['password'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
$data = mysqli_fetch_array($query);
$check = mysqli_num_rows($query);
if($check > 0){
    $_SESSION['user_id'] = $data['user_id'];
    // $_SESSION['id_pasien'] = $data['user_id'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['nama'] = $data['nama'];
    // if($data['role'] == 'dokter'){
    //     header("Location: dashboard_dokter.php");
    // } else{
    //     header("location: index.php");
    // }
     header("location: index.php");
}

else {
    // header("location:signin.php?condition=failedlogin");
    echo "
    <html>
    <head>
        <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'Failed',
                title: 'Gagal!',
                text: 'Email atau password salah.',
            }).then(() => {
                window.location.href = 'signin.php';
            });
        </script>
    </body>
    </html>";
}

?>