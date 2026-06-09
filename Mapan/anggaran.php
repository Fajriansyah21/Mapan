<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$gaji = (float) $_SESSION['gaji_bulanan'];
$metode = isset($_POST['metode']) ? $_POST['metode'] : '50/30/20';
$custom_kebutuhan = isset($_POST['custom_kebutuhan']) ? (float) $_POST['custom_kebutuhan'] : 50;
$custom_keinginan = isset($_POST['custom_keinginan']) ? (float) $_POST['custom_keinginan'] : 30;
$custom_tabungan = isset($_POST['custom_tabungan']) ? (float) $_POST['custom_tabungan'] : 20;

if ($metode == '60/20/20') {
    $kebutuhan = 60;
    $keinginan = 20;
    $tabungan = 20;
} elseif ($metode == 'Custom') {
    $kebutuhan = $custom_kebutuhan;
    $keinginan = $custom_keinginan;
    $tabungan = $custom_tabungan;
} else {
    $kebutuhan = 50;
    $keinginan = 30;
    $tabungan = 20;
}

$total_persen = $kebutuhan + $keinginan + $tabungan;
render_header("Simulasi Anggaran", "anggaran");
?>

<section class="grid grid-2">
    <div class="card">
        <h2>Budget Planner</h2>
        <p class="muted">Pilih metode pembagian gaji untuk melihat alokasi kebutuhan, keinginan, dan tabungan.</p>
        <form method="POST">
            <label>Metode
                <select name="metode">
                    <option <?php echo $metode == '50/30/20' ? 'selected' : ''; ?>>50/30/20</option>
                    <option <?php echo $metode == '60/20/20' ? 'selected' : ''; ?>>60/20/20</option>
                    <option <?php echo $metode == 'Custom' ? 'selected' : ''; ?>>Custom</option>
                </select>
            </label>
            <div class="grid grid-3">
                <label>Kebutuhan %
                    <input type="number" name="custom_kebutuhan" value="<?php echo $custom_kebutuhan; ?>">
                </label>
                <label>Keinginan %
                    <input type="number" name="custom_keinginan" value="<?php echo $custom_keinginan; ?>">
                </label>
                <label>Tabungan %
                    <input type="number" name="custom_tabungan" value="<?php echo $custom_tabungan; ?>">
                </label>
            </div>
            <button name="hitung">Hitung Anggaran</button>
        </form>
    </div>

    <div class="card">
        <h2>Hasil Pembagian</h2>
        <?php if ($total_persen != 100) { ?>
            <p><span class="badge warning">Total persentase <?php echo $total_persen; ?>%</span></p>
            <p class="muted">Untuk metode custom, total idealnya 100% agar seluruh gaji terbagi rapi.</p>
        <?php } ?>
        <div class="grid">
            <div class="metric">
                <span class="label">Kebutuhan <?php echo $kebutuhan; ?>%</span>
                <span class="value"><?php echo rupiah($gaji * $kebutuhan / 100); ?></span>
            </div>
            <div class="metric">
                <span class="label">Keinginan <?php echo $keinginan; ?>%</span>
                <span class="value"><?php echo rupiah($gaji * $keinginan / 100); ?></span>
            </div>
            <div class="metric">
                <span class="label">Tabungan <?php echo $tabungan; ?>%</span>
                <span class="value"><?php echo rupiah($gaji * $tabungan / 100); ?></span>
            </div>
        </div>
    </div>
</section>

<?php render_footer(); ?>
