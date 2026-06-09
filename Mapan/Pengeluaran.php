<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();
ensure_pengeluaran_schema($koneksi);

$id_user = $_SESSION['id_user'];
$data = mysqli_query(
    $koneksi,
    "SELECT * FROM pengeluaran
    WHERE id_user='$id_user'
    ORDER BY tanggal DESC"
);

render_header("Pengeluaran Bulanan", "pengeluaran");
?>

<div class="actions">
    <a class="btn" href="tambah_pengeluaran.php">Tambah Pengeluaran</a>
</div>

<br>

<div class="card">
    <table>
        <tr>
            <th>No</th>
            <th>Kategori</th>
            <th>Nama</th>
            <th>Jumlah Barang</th>
            <th>Harga Satuan</th>
            <th>Total</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($data)) {
        ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><span class="badge info"><?php echo htmlspecialchars($row['kategori']); ?></span></td>
                <td><?php echo htmlspecialchars($row['nama_pengeluaran']); ?></td>
                <td><?php echo rtrim(rtrim(number_format($row['jumlah_barang'], 2, ',', '.'), '0'), ','); ?> <?php echo htmlspecialchars($row['satuan']); ?></td>
                <td><?php echo rupiah($row['harga_satuan']); ?></td>
                <td><strong><?php echo rupiah($row['jumlah']); ?></strong></td>
                <td><?php echo format_tanggal($row['tanggal']); ?></td>
                <td>
                    <a href="edit_pengeluaran.php?id=<?php echo $row['id_pengeluaran']; ?>">Edit</a>
                    |
                    <a href="hapus_pengeluaran.php?id=<?php echo $row['id_pengeluaran']; ?>" onclick="return confirm('Hapus data ini?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php render_footer(); ?>
