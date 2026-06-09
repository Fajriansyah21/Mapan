<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$id_user = $_SESSION['id_user'];
$data = mysqli_query(
    $koneksi,
    "SELECT * FROM target_tabungan
    WHERE id_user='$id_user'
    ORDER BY nominal_target DESC"
);

render_header("Rencana Tabungan", "tabungan");
?>

<div class="card">
    <h2>Apa itu Rencana Tabungan?</h2>
    <p class="muted">
        Rencana Tabungan adalah fitur untuk mencatat tujuan keuangan, misalnya membeli laptop,
        motor, HP, liburan, atau menyiapkan dana darurat. Kamu cukup mengisi total uang yang
        dibutuhkan dan berapa uang yang sanggup ditabung setiap bulan. Sistem akan menghitung
        estimasi berapa bulan tujuan itu bisa tercapai.
    </p>
</div>

<br>

<div class="actions">
    <a class="btn" href="tambah_target.php">Tambah Rencana Tabungan</a>
    <a class="btn secondary" href="dana_darurat.php">Dana Darurat</a>
</div>

<br>

<div class="card">
    <table>
        <tr>
            <th>No</th>
            <th>Tujuan Tabungan</th>
            <th>Total Uang yang Dibutuhkan</th>
            <th>Rencana Menabung per Bulan</th>
            <th>Perkiraan Waktu Tercapai</th>
            <th>Aksi</th>
        </tr>
        <?php
        $no = 1;
        while ($row = mysqli_fetch_assoc($data)) {
            $estimasi = $row['tabungan_per_bulan'] > 0 ? ceil($row['nominal_target'] / $row['tabungan_per_bulan']) : 0;
        ?>
            <tr>
                <td><?php echo $no++; ?></td>
                <td><?php echo htmlspecialchars($row['nama_target']); ?></td>
                <td><?php echo rupiah($row['nominal_target']); ?></td>
                <td><?php echo rupiah($row['tabungan_per_bulan']); ?></td>
                <td>
                    <strong><?php echo $estimasi; ?> bulan</strong>
                </td>
                <td>
                    <a href="edit_target.php?id=<?php echo $row['id_target']; ?>">Edit</a>
                    |
                    <a href="hapus_target.php?id=<?php echo $row['id_target']; ?>" onclick="return confirm('Hapus target ini?')">Hapus</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php render_footer(); ?>