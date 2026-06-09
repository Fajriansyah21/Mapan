<?php

function rupiah($angka)
{
    return "Rp" . number_format((float) $angka, 0, ',', '.');
}

function format_tanggal($tanggal)
{
    if (!$tanggal) {
        return "-";
    }

    return date('d-m-Y', strtotime($tanggal));
}

function ensure_pengeluaran_schema($koneksi)
{
    $cek_jumlah_barang = mysqli_query($koneksi, "SHOW COLUMNS FROM pengeluaran LIKE 'jumlah_barang'");
    if ($cek_jumlah_barang && mysqli_num_rows($cek_jumlah_barang) == 0) {
        mysqli_query($koneksi, "ALTER TABLE pengeluaran ADD COLUMN jumlah_barang DECIMAL(10,2) NOT NULL DEFAULT 1 AFTER nama_pengeluaran");
    }

    $cek_satuan = mysqli_query($koneksi, "SHOW COLUMNS FROM pengeluaran LIKE 'satuan'");
    if ($cek_satuan && mysqli_num_rows($cek_satuan) == 0) {
        mysqli_query($koneksi, "ALTER TABLE pengeluaran ADD COLUMN satuan VARCHAR(20) NOT NULL DEFAULT 'pcs' AFTER jumlah_barang");
    }

    $cek_harga_satuan = mysqli_query($koneksi, "SHOW COLUMNS FROM pengeluaran LIKE 'harga_satuan'");
    if ($cek_harga_satuan && mysqli_num_rows($cek_harga_satuan) == 0) {
        mysqli_query($koneksi, "ALTER TABLE pengeluaran ADD COLUMN harga_satuan DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER satuan");
    }

    @mysqli_query($koneksi, "UPDATE pengeluaran SET harga_satuan=jumlah WHERE harga_satuan=0");
}

function wajib_login()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['username'])) {
        header("Location: login.php");
        exit;
    }
}

function ambil_ringkasan($koneksi, $id_user, $gaji)
{
    $query_total = mysqli_query(
        $koneksi,
        "SELECT COALESCE(SUM(jumlah), 0) AS total FROM pengeluaran WHERE id_user='$id_user'"
    );
    $total = (float) mysqli_fetch_assoc($query_total)['total'];

    $query_tabungan = mysqli_query(
        $koneksi,
        "SELECT COALESCE(SUM(tabungan_per_bulan), 0) AS total FROM target_tabungan WHERE id_user='$id_user'"
    );
    $tabungan = (float) mysqli_fetch_assoc($query_tabungan)['total'];

    $sisa = (float) $gaji - $total - $tabungan;
    $rasio_pengeluaran = $gaji > 0 ? ($total / $gaji) * 100 : 0;
    $rasio_tabungan = $gaji > 0 ? ($tabungan / $gaji) * 100 : 0;

    $score = 100;
    if ($rasio_pengeluaran > 80) {
        $score -= 35;
    } elseif ($rasio_pengeluaran > 60) {
        $score -= 20;
    } elseif ($rasio_pengeluaran > 50) {
        $score -= 10;
    }

    if ($rasio_tabungan < 10) {
        $score -= 20;
    } elseif ($rasio_tabungan < 20) {
        $score -= 10;
    }

    if ($sisa < 0) {
        $score -= 25;
    } elseif ($sisa < $gaji * 0.1) {
        $score -= 10;
    }

    $score = max(0, min(100, $score));

    if ($score >= 85) {
        $kategori = "Sangat Sehat";
        $warna = "success";
    } elseif ($score >= 70) {
        $kategori = "Sehat";
        $warna = "info";
    } elseif ($score >= 50) {
        $kategori = "Perlu Perbaikan";
        $warna = "warning";
    } else {
        $kategori = "Berisiko";
        $warna = "danger";
    }

    return [
        'total_pengeluaran' => $total,
        'total_tabungan' => $tabungan,
        'sisa_uang' => $sisa,
        'rasio_pengeluaran' => $rasio_pengeluaran,
        'rasio_tabungan' => $rasio_tabungan,
        'dana_darurat' => $total * 6,
        'score' => $score,
        'kategori_score' => $kategori,
        'warna_score' => $warna
    ];
}

function rekomendasi_otomatis($ringkasan)
{
    $saran = [];

    if ($ringkasan['rasio_pengeluaran'] > 80) {
        $saran[] = "Pengeluaran rutin sudah melewati 80% dari gaji. Kurangi kategori yang paling fleksibel seperti hiburan atau lain-lain.";
    } elseif ($ringkasan['rasio_pengeluaran'] > 50) {
        $saran[] = "Pengeluaran kebutuhan mulai tinggi. Coba targetkan total kebutuhan mendekati 50-60% dari pendapatan.";
    }

    if ($ringkasan['rasio_tabungan'] < 20) {
        $saran[] = "Tabungan masih di bawah 20% dari pendapatan. Naikkan bertahap agar target keuangan lebih cepat tercapai.";
    }

    if ($ringkasan['sisa_uang'] < 0) {
        $saran[] = "Arus kas bulanan minus. Tunda pembelian besar sampai sisa uang kembali positif.";
    } elseif ($ringkasan['sisa_uang'] > 0) {
        $saran[] = "Ada sisa uang " . rupiah($ringkasan['sisa_uang']) . ". Sebagian bisa diarahkan ke dana darurat atau target prioritas.";
    }

    if (count($saran) == 0) {
        $saran[] = "Keuangan bulanan cukup stabil. Pertahankan rasio tabungan dan cek ulang pengeluaran setiap akhir bulan.";
    }

    return $saran;
}

function render_header($judul, $menu_aktif = '')
{
    $nama = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Pengguna';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($judul); ?> - Mapan</title>
    <link rel="stylesheet" href="Assets/style.css">
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            <img src="Assets/logo.png" alt="Logo Mapan">
            <div>
                <strong>Mapan</strong>
                <span>Budget simulator</span>
            </div>
        </div>
        <nav class="nav">
            <a class="<?php echo $menu_aktif == 'dashboard' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
            <a class="<?php echo $menu_aktif == 'profil' ? 'active' : ''; ?>" href="profil_gaji.php">Profil & Gaji</a>
            <a class="<?php echo $menu_aktif == 'pengeluaran' ? 'active' : ''; ?>" href="Pengeluaran.php">Pengeluaran</a>
            <a class="<?php echo $menu_aktif == 'anggaran' ? 'active' : ''; ?>" href="anggaran.php">Anggaran</a>
            <a class="<?php echo $menu_aktif == 'tabungan' ? 'active' : ''; ?>" href="target_tabungan.php">Rencana Tabungan</a>
            <a class="<?php echo $menu_aktif == 'cicilan' ? 'active' : ''; ?>" href="simulasi_cicilan.php">Cicilan</a>
            <a class="<?php echo $menu_aktif == 'dana' ? 'active' : ''; ?>" href="dana_darurat.php">Dana Darurat</a>
            <a class="<?php echo $menu_aktif == 'laporan' ? 'active' : ''; ?>" href="laporan.php">Laporan</a>
            <a class="<?php echo $menu_aktif == 'beli' ? 'active' : ''; ?>" href="simulasi_pembelian.php">Simulasi Beli</a>
            <a href="logout.php">Logout</a>
        </nav>
    </aside>
    <main class="main">
        <div class="topbar">
            <div>
                <p class="eyebrow">Halo, <?php echo htmlspecialchars($nama); ?></p>
                <h1><?php echo htmlspecialchars($judul); ?></h1>
            </div>
            <div class="topbar-actions">
                <button class="theme-toggle" id="themeToggle" type="button" title="Ganti mode terang/gelap">Gelap</button>
                <?php if ($menu_aktif != 'dashboard') { ?>
                    <a class="btn secondary" href="dashboard.php">Dashboard</a>
                <?php } ?>
            </div>
        </div>
<?php
}

function render_footer()
{
?>
    </main>
</div>
<script>
    const themeToggle = document.getElementById('themeToggle');

    function setTheme(mode) {
        document.body.classList.toggle('dark-mode', mode === 'dark');
        localStorage.setItem('theme', mode);
        if (themeToggle) {
            themeToggle.textContent = mode === 'dark' ? 'Terang' : 'Gelap';
            themeToggle.title = mode === 'dark' ? 'Ganti ke mode terang' : 'Ganti ke mode gelap';
        }
    }

    setTheme(localStorage.getItem('theme') === 'dark' ? 'dark' : 'light');

    if (themeToggle) {
        themeToggle.addEventListener('click', function () {
            const nextMode = document.body.classList.contains('dark-mode') ? 'light' : 'dark';
            setTheme(nextMode);
        });
    }
</script>
</body>
</html>
<?php
}
