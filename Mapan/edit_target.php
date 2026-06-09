<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$id_user = $_SESSION['id_user'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM target_tabungan WHERE id_target='$id' AND id_user='$id_user'"
);

if (mysqli_num_rows($data) == 0) {
    header("Location: target_tabungan.php");
    exit;
}

$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $nama_target = htmlspecialchars(trim($_POST['nama_target']));
    $nominal_target = (float) $_POST['nominal_target'];
    $tabungan_per_bulan = (float) $_POST['tabungan_per_bulan'];

    if ($nama_target != '' && $nominal_target > 0 && $tabungan_per_bulan > 0) {
        mysqli_query(
            $koneksi,
            "UPDATE target_tabungan SET
            nama_target='$nama_target',
            nominal_target='$nominal_target',
            tabungan_per_bulan='$tabungan_per_bulan'
            WHERE id_target='$id' AND id_user='$id_user'"
        );
        header("Location: target_tabungan.php");
        exit;
    } else {
        $error = "Semua data wajib diisi dengan benar.";
    }
}

render_header("Edit Rencana Tabungan", "tabungan");
?>

<div class="card">
    <h2>Perbarui Rencana Tabungan</h2>
    <p class="muted">
        Ubah tujuan, total uang yang dibutuhkan, atau kemampuan menabung per bulan.
        Estimasi waktu di halaman daftar akan otomatis mengikuti data terbaru.
    </p>
    <br>
    <?php if (isset($error)) { ?><div class="alert"><?php echo $error; ?></div><?php } ?>
    <form method="POST">
        <label>Tujuan tabungan
            <input type="text" name="nama_target" value="<?php echo htmlspecialchars($row['nama_target']); ?>" required>
        </label>
        <label>Total uang yang dibutuhkan
            <input type="number" name="nominal_target" value="<?php echo $row['nominal_target']; ?>" min="1" required>
        </label>
        <label>Rencana menabung per bulan
            <input type="number" name="tabungan_per_bulan" value="<?php echo $row['tabungan_per_bulan']; ?>" min="1" required>
        </label>
        <div class="actions">
            <button name="update">Update Rencana</button>
            <a class="btn secondary" href="target_tabungan.php">Batal</a>
        </div>
    </form>
</div>

<?php render_footer(); ?>
