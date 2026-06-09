<?php
session_start();
include "layout.php";
wajib_login();

$nama_barang = '';
$harga_barang = 0;
$tabungan = 0;
$bulan = 0;

if (isset($_POST['hitung'])) {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $harga_barang = (float) $_POST['harga_barang'];
    $tabungan = (float) $_POST['tabungan_per_bulan'];
    if ($harga_barang > 0 && $tabungan > 0) {
        $bulan = ceil($harga_barang / $tabungan);
    }
}

render_header("Simulasi Pembelian", "beli");
?>

<section class="grid grid-2">
    <div class="card">
        <h2>Rencana Beli Barang</h2>
        <form method="POST">
            <label>Nama barang
                <input type="text" name="nama_barang" value="<?php echo $nama_barang; ?>" required>
            </label>
            <label>Harga barang
                <input type="number" name="harga_barang" value="<?php echo $harga_barang; ?>" min="1" required>
            </label>
            <label>Tabungan per bulan
                <input type="number" name="tabungan_per_bulan" value="<?php echo $tabungan; ?>" min="1" required>
            </label>
            <button name="hitung">Hitung</button>
        </form>
    </div>

    <div class="card">
        <h2>Hasil Simulasi</h2>
        <?php if ($bulan > 0) { ?>
            <p class="metric">
                <span class="label"><?php echo $nama_barang; ?> bisa dibeli dalam</span>
                <span class="value"><?php echo $bulan; ?> bulan</span>
            </p>
            <p class="muted">Dengan tabungan <?php echo rupiah($tabungan); ?> per bulan untuk harga <?php echo rupiah($harga_barang); ?>.</p>
        <?php } else { ?>
            <p class="muted">Isi data barang untuk melihat estimasi waktu pembelian.</p>
        <?php } ?>
    </div>
</section>

<?php render_footer(); ?>
