<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "sehatjiwaNew";

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi ---connec
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>