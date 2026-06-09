<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$id_user = $_SESSION['id_user'];
$gaji = (float) $_SESSION['gaji_bulanan'];
$ringkasan = ambil_ringkasan($koneksi, $id_user, $gaji);
$saran = rekomendasi_otomatis($ringkasan);

$kategori_query = mysqli_query(
    $koneksi,
    "SELECT kategori, COALESCE(SUM(jumlah), 0) AS total
    FROM pengeluaran
    WHERE id_user='$id_user'
    GROUP BY kategori
    ORDER BY total DESC
    LIMIT 5"
);

render_header("Dashboard Keuangan", "dashboard");
?>

<section class="grid grid-4">
    <div class="card metric">
        <span class="label">Gaji bulanan</span>
        <span class="value"><?php echo rupiah($gaji); ?></span>
    </div>
    <div class="card metric">
        <span class="label">Total pengeluaran</span>
        <span class="value"><?php echo rupiah($ringkasan['total_pengeluaran']); ?></span>
    </div>
    <div class="card metric">
        <span class="label">Target tabungan/bulan</span>
        <span class="value"><?php echo rupiah($ringkasan['total_tabungan']); ?></span>
    </div>
    <div class="card metric">
        <span class="label">Sisa uang</span>
        <span class="value"><?php echo rupiah($ringkasan['sisa_uang']); ?></span>
    </div>
</section>

<br>

<section class="grid grid-2">
    <div class="card">
        <h2>Financial Health Score</h2>
        <p class="muted">Skor dihitung dari rasio pengeluaran, tabungan, dan sisa uang bulanan.</p>
        <div class="metric">
            <span class="value"><?php echo $ringkasan['score']; ?>/100</span>
        </div>
        <p>
            <span class="badge <?php echo $ringkasan['warna_score']; ?>">
                <?php echo $ringkasan['kategori_score']; ?>
            </span>
        </p>
        <div class="progress" style="--value: <?php echo $ringkasan['score']; ?>%;">
            <span></span>
        </div>
    </div>

    <div class="card">
        <h2>Rekomendasi Otomatis</h2>
        <ul class="recommendations">
            <?php foreach ($saran as $item) { ?>
                <li><?php echo htmlspecialchars($item); ?></li>
            <?php } ?>
        </ul>
    </div>
</section>

<br>

<section class="grid grid-2">
    <div class="card">
        <h2>Pengeluaran Terbesar</h2>
        <?php if (mysqli_num_rows($kategori_query) == 0) { ?>
            <p class="muted">Belum ada data pengeluaran. Tambahkan data agar grafik muncul.</p>
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
        <h2>Aksi Cepat</h2>
        <p class="muted">Gunakan menu ini untuk mengisi dan mensimulasikan keputusan keuangan.</p>
        <div class="actions">
            <a class="btn" href="profil_gaji.php">Ubah Gaji</a>
            <a class="btn" href="tambah_pengeluaran.php">Tambah Pengeluaran</a>
            <a class="btn secondary" href="anggaran.php">Budget Planner</a>
            <a class="btn secondary" href="simulasi_cicilan.php">Simulasi Cicilan</a>
            <a class="btn secondary" href="target_tabungan.php">Rencana Tabungan</a>
        </div>
    </div>
</section>

<?php render_footer(); ?>
