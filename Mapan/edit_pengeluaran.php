<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();
ensure_pengeluaran_schema($koneksi);

$id_user = $_SESSION['id_user'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$data = mysqli_query(
    $koneksi,
    "SELECT * FROM pengeluaran WHERE id_pengeluaran='$id' AND id_user='$id_user'"
);

if (mysqli_num_rows($data) == 0) {
    header("Location: Pengeluaran.php");
    exit;
}

$row = mysqli_fetch_assoc($data);

if (isset($_POST['update'])) {
    $kategori = htmlspecialchars(trim($_POST['kategori']));
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jumlah_barang = (float) $_POST['jumlah_barang'];
    $satuan = htmlspecialchars(trim($_POST['satuan']));
    $harga_satuan = (float) $_POST['harga_satuan'];
    $jumlah = $jumlah_barang * $harga_satuan;
    $tanggal = $_POST['tanggal'];

    if ($kategori != '' && $nama != '' && $jumlah_barang > 0 && $satuan != '' && $harga_satuan > 0 && $tanggal != '') {
        mysqli_query(
            $koneksi,
            "UPDATE pengeluaran SET
            kategori='$kategori',
            nama_pengeluaran='$nama',
            jumlah_barang='$jumlah_barang',
            satuan='$satuan',
            harga_satuan='$harga_satuan',
            jumlah='$jumlah',
            tanggal='$tanggal'
            WHERE id_pengeluaran='$id' AND id_user='$id_user'"
        );

        header("Location: Pengeluaran.php");
        exit;
    } else {
        $error = "Semua data wajib diisi dengan benar.";
    }
}

render_header("Edit Pengeluaran", "pengeluaran");
?>

<div class="card">
    <?php if (isset($error)) { ?><div class="alert"><?php echo $error; ?></div><?php } ?>
    <form method="POST">
        <label>Kategori
            <select name="kategori" required>
                <?php
                $kategori_list = ['Makan', 'Transportasi', 'Kos/Kontrakan', 'Internet', 'Listrik', 'Air', 'Pulsa', 'Hiburan', 'Lain-lain'];
                foreach ($kategori_list as $kategori) {
                    $selected = $row['kategori'] == $kategori ? 'selected' : '';
                    echo "<option $selected>$kategori</option>";
                }
                ?>
            </select>
        </label>
        <label>Nama pengeluaran
            <input type="text" name="nama" value="<?php echo htmlspecialchars($row['nama_pengeluaran']); ?>" required>
        </label>
        <div class="field-row">
            <label>Jumlah barang
                <input type="number" name="jumlah_barang" value="<?php echo $row['jumlah_barang']; ?>" min="0.01" step="0.01" required>
            </label>
            <label>Satuan
                <select name="satuan" required>
                    <?php
                    $satuan_list = ['pcs', 'porsi', 'paket', 'kg', 'liter', 'bulan', 'kali'];
                    foreach ($satuan_list as $satuan) {
                        $selected = $row['satuan'] == $satuan ? 'selected' : '';
                        echo "<option $selected>$satuan</option>";
                    }
                    ?>
                </select>
            </label>
            <label>Harga satuan
                <input type="number" name="harga_satuan" value="<?php echo $row['harga_satuan']; ?>" min="1" required>
            </label>
        </div>
        <p class="help">Total pengeluaran akan diperbarui dari jumlah barang dikali harga satuan.</p>
        <div class="total-preview" id="preview-total">Total: Rp0</div>
        <label>Tanggal
            <input type="date" name="tanggal" value="<?php echo $row['tanggal']; ?>" required>
        </label>
        <div class="actions">
            <button name="update">Update</button>
            <a class="btn secondary" href="Pengeluaran.php">Batal</a>
        </div>
    </form>
</div>

<script>
    const jumlahInput = document.querySelector('[name="jumlah_barang"]');
    const hargaInput = document.querySelector('[name="harga_satuan"]');
    const previewTotal = document.getElementById('preview-total');

    function formatRupiah(value) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        }).format(value || 0);
    }

    function updateTotal() {
        const jumlah = parseFloat(jumlahInput.value) || 0;
        const harga = parseFloat(hargaInput.value) || 0;
        previewTotal.textContent = 'Total: ' + formatRupiah(jumlah * harga);
    }

    jumlahInput.addEventListener('input', updateTotal);
    hargaInput.addEventListener('input', updateTotal);
    updateTotal();
</script>

<?php render_footer(); ?>
