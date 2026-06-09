<?php
session_start();
include "koneksi.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

mysqli_query(
    $koneksi,
    "DELETE FROM pengeluaran WHERE id_pengeluaran='$id' AND id_user='$id_user'"
);

header("Location: Pengeluaran.php");
exit;
?>
