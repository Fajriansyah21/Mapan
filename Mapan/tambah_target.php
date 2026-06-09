<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

if (isset($_POST['simpan'])) {
    $id_user = $_SESSION['id_user'];
    $nama_target = htmlspecialchars(trim($_POST['nama_target']));
    $nominal_target = (float) $_POST['nominal_target'];
    $tabungan_per_bulan = (float) $_POST['tabungan_per_bulan'];

    if ($nama_target != '' && $nominal_target > 0 && $tabungan_per_bulan > 0) {
        mysqli_query(
            $koneksi,
            "INSERT INTO target_tabungan VALUES(NULL, '$id_user', '$nama_target', '$nominal_target', '$tabungan_per_bulan')"
        );
        header("Location: target_tabungan.php");
        exit;
    } else {
        $error = "Semua data wajib diisi dengan benar.";
    }
}

render_header("Tambah Rencana Tabungan", "tabungan");
?>

<div class="card">
    <h2>Isi Rencana Tabungan</h2>
    <p class="muted">
        Gunakan form ini untuk mencatat barang atau tujuan yang ingin kamu capai.
        Contoh: Laptop Rp12.000.000 dengan rencana menabung Rp1.000.000 per bulan,
        maka estimasinya tercapai dalam 12 bulan.
    </p>
    <br>
    <?php if (isset($error)) { ?><div class="alert"><?php echo $error; ?></div><?php } ?>
    <form method="POST">
        <label class="text-hitam">Tujuan tabungan
            <input type="text" name="nama_target" placeholder="Contoh: Laptop, HP baru, dana darurat" required>
        </label>
 
        <label class="text-hitam">Total uang yang dibutuhkan
            <input type="number" name="nominal_target" min="1" placeholder="Contoh: 12000000" required>
        </label>

        <label class="text-hitam">Rencana menabung per bulan
            <input type="number" name="tabungan_per_bulan" min="1" placeholder="Contoh: 1000000" required>
        </label>

        <div class="actions">
            <button name="simpan">Simpan Rencana</button>
            <a class="btn secondary" href="target_tabungan.php">Batal</a>
        </div>
    </form>
</div>

<?php render_footer(); ?>
