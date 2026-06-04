<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

include "koneksi.php";

$id_user = $_SESSION['id_user'];

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM pengeluaran
    WHERE id_user='$id_user'
    ORDER BY tanggal DESC"
);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Data Pengeluaran</title>
</head>

<body>

    <h2>Data Pengeluaran</h2>

    <a href="dashboard.php">Kembali Dashboard</a>
    |
    <a href="tambah_pengeluaran.php">Tambah Pengeluaran</a>

    <br><br>

    <table border="1" cellpadding="10">

        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>

        <?php

        $no = 1;

        while ($row = mysqli_fetch_assoc($data)) {

        ?>

            <tr>

                <td><?php echo $no++; ?></td>

                <td><?php echo $row['kategori']; ?></td>

                <td><?php echo $row['nama_pengeluaran']; ?></td>

                <td>
                    Rp<?php echo number_format($row['jumlah'], 0, ',', '.'); ?>
                </td>

                <td><?php echo $row['tanggal']; ?></td>

                <td>

                    <a href="edit_pengeluaran.php?id=<?php echo $row['id_pengeluaran']; ?>">
                        Edit
                    </a>

                    |

                    <a href="hapus_pengeluaran.php?id=<?php echo $row['id_pengeluaran']; ?>">
                        Hapus
                    </a>

                </td>

            </tr>

        <?php } ?>

    </table>

</body>

</html>
