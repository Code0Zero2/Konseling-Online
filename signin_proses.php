<?php 
include 'connection.php';
$email = $_POST['email'];
$password = $_POST['password'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
$data = mysqli_fetch_array($query);
$check = mysqli_num_rows($query);
if($check > 0){
    session_start();
    $_SESSION['id_pasien'] = $data['user_id'];
    if($data['role'] == 'dokter'){
        header("Location: dashboard_dokter.php");
    } else{
        header("location: index.php");
    }
}

else {
    header("location:signin.php?condition=failedlogin");
}

?>