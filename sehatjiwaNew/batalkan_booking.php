<?php
include 'connection.php';
if(isset($_POST['booking_id'])) {
    $id = $_POST['booking_id'];
    mysqli_query($conn, "UPDATE booking SET status='dibatalkan' WHERE booking_id='$id'");
    echo "success";
}
?>
