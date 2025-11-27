<?php
session_start();
include "connection.php";

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    echo "ERROR: User tidak login";
    exit;
}

$pasien_id = $_SESSION['user_id'];

// Validasi POST
if (!isset($_POST['booking_id'], $_POST['rating'], $_POST['pesan'])) {
    echo "ERROR: Data tidak lengkap";
    exit;
}

$booking_id = $_POST['booking_id'];
$rating     = $_POST['rating'];
$testimoni  = $_POST['pesan'];

// Ambil dokter ID berdasarkan booking
$q = mysqli_query($conn, "SELECT dokter_id FROM booking WHERE booking_id='$booking_id'");
$data = mysqli_fetch_assoc($q);

if (!$data) {
    echo "ERROR: booking_id tidak ditemukan";
    exit;
}

$dokter_id = $data['dokter_id'];

// 1️⃣ Cek apakah pasien sudah pernah memberi testimoni untuk dokter ini
$cek = mysqli_query($conn, "
    SELECT rating_id FROM rating_testimoni
    WHERE pasien_id='$pasien_id' AND dokter_id='$dokter_id'
");

if (mysqli_num_rows($cek) > 0) {

    // UPDATE
    $sql = "
        UPDATE rating_testimoni 
        SET rating='$rating', testimoni='$testimoni', tanggal=NOW()
        WHERE pasien_id='$pasien_id' AND dokter_id='$dokter_id'
    ";

    if (mysqli_query($conn, $sql)) {
        echo "UPDATE_SUCCESS";
    } else {
        echo "ERROR: " . mysqli_error($conn);
    }

} else {

    // INSERT
    $sql = "
        INSERT INTO rating_testimoni (pasien_id, dokter_id, rating, testimoni, tanggal)
        VALUES ('$pasien_id', '$dokter_id', '$rating', '$testimoni', NOW())
    ";

    if (mysqli_query($conn, $sql)) {
        echo "INSERT_SUCCESS";
    } else {
        echo "ERROR: " . mysqli_error($conn);
    }
}

?>
