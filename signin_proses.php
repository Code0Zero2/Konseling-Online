<?php 
include 'connection.php';
$email = $_POST['email'];
$password = $_POST['password'];
$query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND password='$password'");
$check = mysqli_num_rows($query);
if($check > 0){
    session_start();
    $_SESSION['email'] = $email;
    header("location:index.php");
} else {
    header("location:signin.php?condition=failedlogin");
}

?>