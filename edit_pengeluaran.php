<?php

session_start();
include "koneksi.php";

$id = $_GET['id'];

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM pengeluaran
    WHERE id_pengeluaran='$id'"
);

$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {

    $kategori = htmlspecialchars(trim($_POST['kategori']));
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jumlah = $_POST['jumlah'];
    $tanggal = $_POST['tanggal'];

    mysqli_query(
        $koneksi,
        "UPDATE pengeluaran SET
        kategori='$kategori',
        nama_pengeluaran='$nama',
        jumlah='$jumlah',
        tanggal='$tanggal'
        WHERE id_pengeluaran='$id'"
    );

    header("Location: pengeluaran.php");
}

?>

<h2>Edit Pengeluaran</h2>

<form method="POST">

    Kategori
    <br>
    <input
        type="text"
        name="kategori"
        value="<?php echo $row['kategori']; ?>">

    <br><br>

    Nama Pengeluaran
    <br>
    <input
        type="text"
        name="nama"
        value="<?php echo $row['nama_pengeluaran']; ?>">

    <br><br>

    Jumlah
    <br>
    <input
        type="number"
        name="jumlah"
        value="<?php echo $row['jumlah']; ?>">

    <br><br>

    Tanggal
    <br>
    <input
        type="date"
        name="tanggal"
        value="<?php echo $row['tanggal']; ?>">

    <br><br>

    <button name="update">
        Update
    </button>

</form>