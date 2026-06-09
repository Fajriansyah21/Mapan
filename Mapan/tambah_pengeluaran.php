<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();
ensure_pengeluaran_schema($koneksi);

if (isset($_POST['simpan'])) {
    $id_user = $_SESSION['id_user'];
    $kategori = htmlspecialchars(trim($_POST['kategori']));
    $nama = htmlspecialchars(trim($_POST['nama']));
    $jumlah_barang = (float) $_POST['jumlah_barang'];
    $satuan = htmlspecialchars(trim($_POST['satuan']));
    $harga_satuan = (float) $_POST['harga_satuan'];
    $total = $jumlah_barang * $harga_satuan;
    $tanggal = $_POST['tanggal'];

    if ($kategori != '' && $nama != '' && $jumlah_barang > 0 && $satuan != '' && $harga_satuan > 0 && $tanggal != '') {
        mysqli_query(
            $koneksi,
            "INSERT INTO pengeluaran
            (id_pengeluaran, id_user, kategori, nama_pengeluaran, jumlah_barang, satuan, harga_satuan, jumlah, tanggal)
            VALUES(NULL, '$id_user', '$kategori', '$nama', '$jumlah_barang', '$satuan', '$harga_satuan', '$total', '$tanggal')"
        );
        header("Location: Pengeluaran.php");
        exit;
    } else {
        $error = "Semua data wajib diisi dengan benar.";
    }
}

render_header("Tambah Pengeluaran", "pengeluaran");
?>

<div class="card">
    <?php if (isset($error)) { ?><div class="alert"><?php echo $error; ?></div><?php } ?>
    <form method="POST">
        <label>Kategori
            <select name="kategori" required>
                <option value="">Pilih kategori</option>
                <option>Makan</option>
                <option>Transportasi</option>
                <option>Kos/Kontrakan</option>
                <option>Internet</option>
                <option>Listrik</option>
                <option>Air</option>
                <option>Pulsa</option>
                <option>Hiburan</option>
                <option>Lain-lain</option>
            </select>
        </label>
        <label>Nama pengeluaran
            <input type="text" name="nama" placeholder="Contoh: Burger, bensin, kuota internet" required>
        </label>
        <div class="field-row">
            <label>Jumlah barang
                <input type="number" name="jumlah_barang" min="0.01" step="0.01" placeholder="5" required>
            </label>
            <label>Satuan
                <select name="satuan" required>
                    <option>pcs</option>
                    <option>porsi</option>
                    <option>paket</option>
                    <option>kg</option>
                    <option>liter</option>
                    <option>bulan</option>
                    <option>kali</option>
                </select>
            </label>
            <label>Harga satuan
                <input type="number" name="harga_satuan" min="1" placeholder="10000" required>
            </label>
        </div>
        <p class="help">Contoh: Burger, jumlah 5 pcs, harga satuan Rp10.000. Total akan dihitung otomatis oleh sistem.</p>
        <div class="total-preview" id="preview-total">Total: Rp0</div>
        <label>Tanggal
            <input type="date" name="tanggal" required>
        </label>
        <div class="actions">
            <button name="simpan">Simpan</button>
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
</script>

<?php render_footer(); ?>
