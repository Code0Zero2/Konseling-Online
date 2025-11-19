<?php
include 'connection.php';

// Ambil ID Dokter dari URL (lebih aman menggunakan intval)
if (isset($_GET['id_dokter'])) {
    $dokter_id = intval($_GET['id_dokter']);
} else {
    die("<h2>Dokter tidak ditemukan.</h2>");
}

// Ambil data dokter
$sql_dokter = "
    SELECT user_id, nama, email, no_hp 
    FROM users 
    WHERE user_id = '$dokter_id' AND role='dokter'
";
$result_dokter = mysqli_query($conn, $sql_dokter);
$dokter = mysqli_fetch_assoc($result_dokter);

if (!$dokter) {
    die("<h2>Data dokter tidak ditemukan.</h2>");
}

// Ambil artikel yang ditulis dokter
$sql_artikel = "
    SELECT artikel_id, judul, tanggal 
    FROM artikel
    WHERE dokter_id = '$dokter_id'
    ORDER BY tanggal DESC
";
$artikel_result = mysqli_query($conn, $sql_artikel);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .dokter-card {
    max-width: 700px;
    margin: 30px auto;
    padding: 25px;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
    font-family: Arial, sans-serif;
}

.dokter-header {
    display: flex;
    align-items: center;
    gap: 20px;
}

.dokter-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #4CAF50;
}

.dokter-info h2 {
    margin: 0;
    font-size: 26px;
    color: #333;
}

.dokter-info p {
    margin: 3px 0;
    color: #555;
}

hr {
    margin: 20px 0;
    border: none;
    border-top: 1px solid #ddd;
}

h3 {
    color: #333;
    margin-bottom: 10px;
}

.artikel-list {
    padding-left: 20px;
}

.artikel-list li {
    margin-bottom: 10px;
}

.artikel-list a {
    text-decoration: none;
    color: #007bff;
    font-weight: bold;
}

.artikel-list a:hover {
    text-decoration: underline;
}

    </style>
</head>
<body>
    <div class="dokter-card">
    <div class="dokter-header">
        <div class="dokter-info">
            <h2><?php echo $dokter['nama']; ?></h2>
            <p>Email: <?php echo $dokter['email']; ?></p>
            <p>No. Telp: <?php echo $dokter['no_hp']; ?></p>
        </div>
    </div>

    <hr>

    <h3>Artikel yang Pernah Ditulis</h3>

    <?php if (mysqli_num_rows($artikel_result) > 0) { ?>
        <ul class="artikel-list">
            <?php while($row = mysqli_fetch_assoc($artikel_result)) { ?>
                <li>
                    <a href="artikel_detail.php?id=<?= $row['artikel_id']; ?>">
                        <?= $row['judul']; ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    <?php } else { ?>
        <p>Tidak ada artikel yang ditulis dokter ini.</p>
    <?php } ?>
</div>

</body>
</html>