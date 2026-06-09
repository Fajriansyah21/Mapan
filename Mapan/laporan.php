<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$id_user = $_SESSION['id_user'];
$gaji = (float) $_SESSION['gaji_bulanan'];
$ringkasan = ambil_ringkasan($koneksi, $id_user, $gaji);

$kategori_query = mysqli_query(
    $koneksi,
    "SELECT kategori, COALESCE(SUM(jumlah), 0) AS total
    FROM pengeluaran
    WHERE id_user='$id_user'
    GROUP BY kategori
    ORDER BY total DESC"
);

$target_query = mysqli_query(
    $koneksi,
    "SELECT * FROM target_tabungan
    WHERE id_user='$id_user'
    ORDER BY nominal_target DESC"
);

render_header("Laporan Keuangan", "laporan");
?>

<section class="grid grid-3">
    <div class="card metric">
        <span class="label">Rasio pengeluaran</span>
        <span class="value"><?php echo round($ringkasan['rasio_pengeluaran'], 1); ?>%</span>
    </div>
    <div class="card metric">
        <span class="label">Rasio tabungan</span>
        <span class="value"><?php echo round($ringkasan['rasio_tabungan'], 1); ?>%</span>
    </div>
    <div class="card metric">
        <span class="label">Financial score</span>
        <span class="value"><?php echo $ringkasan['score']; ?>/100</span>
    </div>
</section>

<br>

<section class="grid grid-2">
    <div class="card">
        <h2>Grafik Pengeluaran per Kategori</h2>
        <?php if (mysqli_num_rows($kategori_query) == 0) { ?>
            <p class="muted">Belum ada data pengeluaran.</p>
        <?php } else { ?>
            <?php while ($row = mysqli_fetch_assoc($kategori_query)) {
                $persen = $ringkasan['total_pengeluaran'] > 0 ? ($row['total'] / $ringkasan['total_pengeluaran']) * 100 : 0;
            ?>
                <div class="bar-row">
                    <strong><?php echo htmlspecialchars($row['kategori']); ?></strong>
                    <div class="bar" style="--value: <?php echo round($persen, 2); ?>%;"><span></span></div>
                    <span><?php echo rupiah($row['total']); ?></span>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div class="card">
        <h2>Progress Rencana Tabungan</h2>
        <?php if (mysqli_num_rows($target_query) == 0) { ?>
            <p class="muted">Belum ada target tabungan.</p>
        <?php } else { ?>
            <?php while ($row = mysqli_fetch_assoc($target_query)) {
                $bulan = $row['tabungan_per_bulan'] > 0 ? ceil($row['nominal_target'] / $row['tabungan_per_bulan']) : 0;
                $persen_bulanan = $row['nominal_target'] > 0 ? ($row['tabungan_per_bulan'] / $row['nominal_target']) * 100 : 0;
            ?>
                <div class="bar-row">
                    <strong><?php echo htmlspecialchars($row['nama_target']); ?></strong>
                    <div class="bar" style="--value: <?php echo round($persen_bulanan, 2); ?>%;"><span></span></div>
                    <span><?php echo $bulan; ?> bulan</span>
                </div>
            <?php } ?>
        <?php } ?>
    </div>
</section>

<?php render_footer(); ?>
