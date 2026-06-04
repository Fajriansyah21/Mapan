<?php

session_start();
include "koneksi.php";

if (isset($_POST['simpan'])) {

    $id_user = $_SESSION['id_user'];

    $kategori = htmlspecialchars(trim($_POST['kategori']));
    $nama = htmlspecialchars(trim($_POST['nama']));
    $harga = $_POST['harga'];
    $tanggal = $_POST['tanggal'];

    mysqli_query(
        $koneksi,
        "INSERT INTO pengeluaran
        VALUES(
        NULL,
        '$id_user',
        '$kategori',
        '$nama',
        '$harga',
        '$tanggal'
        )"
    );

    header("Location: pengeluaran.php");
}

?>

<h2>Tambah Pengeluaran</h2>

<form method="POST">

    Kategori
    <br>
    <input type="text" name="kategori">

    <br><br>

    Nama Pengeluaran
    <br>
    <input type="text" name="nama">

    <br><br>

    Harga
    <br>
    <input type="number" name="harga">

    <br><br>

    Tanggal
    <br>
    <input type="date" name="tanggal">

    <br><br>

    <button name="simpan">
        Simpan
    </button>

</form>
