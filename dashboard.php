<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id_user = $_SESSION['id_user'];

$total_pengeluaran = mysqli_query(
    $koneksi,
    "SELECT SUM(jumlah) as total
    FROM pengeluaran
    WHERE id_user='$id_user'"
);

$hasil = mysqli_fetch_assoc($total_pengeluaran);

$total = $hasil['total'];

if ($total == NULL) {
    $total = 0;
}

$sisa_uang = $_SESSION['gaji_bulanan'] - $total;

$persentase = 0;

if ($_SESSION['gaji_bulanan'] > 0) {
    $persentase = ($total / $_SESSION['gaji_bulanan']) * 100;
}

if ($persentase <= 50) {
    $status = "Sehat";
} elseif ($persentase <= 80) {
    $status = "Perlu Perhatian";
} else {
    $status = "Berisiko";
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Dashboard Mapan</title>
</head>

<body style="font-family: Arial;">

    <h2>Dashboard Mapan</h2>

    <p>Selamat datang, <b><?php echo $_SESSION['nama']; ?></b></p>
    <p>Username: <?php echo $_SESSION['username']; ?></p>

    <hr>

    <h3>Ringkasan Keuangan</h3>

    <p>
        Gaji Bulanan :
        Rp<?php echo number_format($_SESSION['gaji_bulanan'], 0, ',', '.'); ?>
    </p>

    <p>
        Total Pengeluaran :
        Rp<?php echo number_format($total, 0, ',', '.'); ?>
    </p>

    <p>
        Sisa Uang :
        Rp<?php echo number_format($sisa_uang, 0, ',', '.'); ?>
    </p>

    <p>
        Status Keuangan :
        <b><?php echo $status; ?></b>
    </p>

    <hr>

    <a href="pengeluaran.php">Kelola Pengeluaran</a>

    <br><br>

    <a href="logout.php">Logout</a>

</body>

</html>