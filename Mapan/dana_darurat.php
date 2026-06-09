<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$id_user = $_SESSION['id_user'];
$gaji = (float) $_SESSION['gaji_bulanan'];
$ringkasan = ambil_ringkasan($koneksi, $id_user, $gaji);
$pengeluaran = isset($_POST['pengeluaran']) ? (float) $_POST['pengeluaran'] : $ringkasan['total_pengeluaran'];
$bulan = isset($_POST['bulan']) ? (int) $_POST['bulan'] : 6;
$ideal = $pengeluaran * $bulan;

render_header("Dana Darurat", "dana");
?>

<section class="grid grid-2">
    <div class="card">
        <h2>Hitung Dana Darurat</h2>
        <p class="muted">Standar umum untuk fresh graduate adalah 3-6 kali pengeluaran bulanan.</p>
        <form method="POST">
            <label>Pengeluaran bulanan
                <input type="number" name="pengeluaran" value="<?php echo $pengeluaran; ?>" min="0">
            </label>
            <label>Jumlah bulan cadangan
                <select name="bulan">
                    <option value="3" <?php echo $bulan == 3 ? 'selected' : ''; ?>>3 bulan</option>
                    <option value="6" <?php echo $bulan == 6 ? 'selected' : ''; ?>>6 bulan</option>
                    <option value="12" <?php echo $bulan == 12 ? 'selected' : ''; ?>>12 bulan</option>
                </select>
            </label>
            <button>Hitung</button>
        </form>
    </div>

    <div class="card metric">
        <span class="label">Dana darurat ideal</span>
        <span class="value"><?php echo rupiah($ideal); ?></span>
        <p class="muted"><?php echo $bulan; ?> x pengeluaran bulanan <?php echo rupiah($pengeluaran); ?></p>
        <p><span class="badge info">Prioritas sebelum cicilan besar</span></p>
    </div>
</section>

<?php render_footer(); ?>
