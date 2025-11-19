<?php 
include 'connection.php';
$nama = $_POST['nama'];
$email = $_POST['email'];
$password = $_POST['password'];
$role = $_POST['role'];
$nohp = $_POST['nohp'];

$query = mysqli_query($conn, "INSERT INTO users(nama, email, password, role, no_hp)
                            VALUE ('$nama', '$email', '$password', '$role', '$nohp')
                        ");
header("location:signin.php");

?>