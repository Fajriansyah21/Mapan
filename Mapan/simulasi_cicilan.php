<?php
session_start();
include "layout.php";
wajib_login();

$hasil = null;

if (isset($_POST['hitung'])) {
    $nama_barang = htmlspecialchars(trim($_POST['nama_barang']));
    $harga_barang = (float) $_POST['harga_barang'];
    $dp = (float) $_POST['dp'];
    $tenor = (int) $_POST['tenor'];
    $gaji = (float) $_SESSION['gaji_bulanan'];

    if ($harga_barang > 0 && $tenor > 0 && $dp >= 0 && $dp < $harga_barang) {
        $sisa_hutang = $harga_barang - $dp;
        $cicilan = $sisa_hutang / $tenor;
        $persentase = $gaji > 0 ? ($cicilan / $gaji) * 100 : 0;

        if ($persentase <= 30) {
            $status = "Aman";
            $warna = "success";
            $pesan = "Cicilan masih dalam batas ideal maksimal 30% dari pendapatan.";
        } elseif ($persentase <= 50) {
            $status = "Perlu Perhatian";
            $warna = "warning";
            $pesan = "Cicilan cukup besar. Pastikan pengeluaran rutin dan tabungan tidak terganggu.";
        } else {
            $status = "Risiko Tinggi";
            $warna = "danger";
            $pesan = "Cicilan melebihi 50% pendapatan. Sebaiknya kurangi harga barang, tambah DP, atau perpanjang tenor.";
        }

        $hasil = [
            'nama_barang' => $nama_barang,
            'sisa_hutang' => $sisa_hutang,
            'cicilan' => $cicilan,
            'persentase' => $persentase,
            'status' => $status,
            'warna' => $warna,
            'pesan' => $pesan
        ];
    }
}

render_header("Simulasi Cicilan", "cicilan");
?>

<section class="grid grid-2">
    <div class="card">
        <h2>Hitung Cicilan</h2>
        <form method="POST">
            <label>Nama barang
                <input type="text" name="nama_barang" required>
            </label>
            <label>Harga barang
                <input type="number" name="harga_barang" min="1" required>
            </label>
            <label>DP
                <input type="number" name="dp" min="0" required>
            </label>
            <label>Tenor (bulan)
                <input type="number" name="tenor" min="1" required>
            </label>
            <button name="hitung">Hitung</button>
        </form>
    </div>

    <div class="card">
        <h2>Analisis Kelayakan</h2>
        <?php if ($hasil) { ?>
            <p><span class="badge <?php echo $hasil['warna']; ?>"><?php echo $hasil['status']; ?></span></p>
            <div class="grid">
                <div class="metric">
                    <span class="label">Cicilan per bulan</span>
                    <span class="value"><?php echo rupiah($hasil['cicilan']); ?></span>
                </div>
                <div class="metric">
                    <span class="label">Rasio terhadap gaji</span>
                    <span class="value"><?php echo round($hasil['persentase'], 2); ?>%</span>
                </div>
                <div class="metric">
                    <span class="label">Sisa hutang</span>
                    <span class="value"><?php echo rupiah($hasil['sisa_hutang']); ?></span>
                </div>
            </div>
            <p class="muted"><?php echo $hasil['pesan']; ?></p>
        <?php } else { ?>
            <p class="muted">Masukkan rencana pembelian untuk melihat cicilan dan risiko keuangannya.</p>
        <?php } ?>
    </div>
</section>

<?php render_footer(); ?>
