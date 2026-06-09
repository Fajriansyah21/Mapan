<?php
session_start();
include "koneksi.php";
include "layout.php";
wajib_login();

$id_user = $_SESSION['id_user'];
$gaji_sekarang = (float) $_SESSION['gaji_bulanan'];

if (isset($_POST['simpan_profil'])) {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password_baru = trim($_POST['password_baru']);
    $konfirmasi_password = trim($_POST['konfirmasi_password']);

    if ($nama == '' || $username == '') {
        $error_profil = "Nama dan username wajib diisi.";
    } else {
        $cek_username = mysqli_query(
            $koneksi,
            "SELECT id_user FROM users WHERE username='$username' AND id_user != '$id_user'"
        );

        if (mysqli_num_rows($cek_username) > 0) {
            $error_profil = "Username sudah digunakan oleh pengguna lain.";
        } elseif ($password_baru != '' && $password_baru != $konfirmasi_password) {
            $error_profil = "Konfirmasi password tidak sama.";
        } elseif ($password_baru != '' && strlen($password_baru) < 4) {
            $error_profil = "Password minimal 4 karakter.";
        } else {
            if ($password_baru != '') {
                mysqli_query(
                    $koneksi,
                    "UPDATE users SET
                    nama='$nama',
                    username='$username',
                    password='$password_baru'
                    WHERE id_user='$id_user'"
                );
            } else {
                mysqli_query(
                    $koneksi,
                    "UPDATE users SET
                    nama='$nama',
                    username='$username'
                    WHERE id_user='$id_user'"
                );
            }

            $_SESSION['nama'] = $nama;
            $_SESSION['username'] = $username;
            $success_profil = "Profil akun berhasil diperbarui.";
        }
    }
}

if (isset($_POST['simpan_gaji'])) {
    $mode = $_POST['mode'];
    $nominal = (float) $_POST['nominal'];

    if ($nominal <= 0) {
        $error = "Nominal harus lebih dari 0.";
    } else {
        if ($mode == 'tambah') {
            $gaji_baru = $gaji_sekarang + $nominal;
        } elseif ($mode == 'kurangi') {
            $gaji_baru = $gaji_sekarang - $nominal;
        } else {
            $gaji_baru = $nominal;
        }

        if ($gaji_baru < 0) {
            $error = "Gaji tidak boleh menjadi minus.";
        } else {
            mysqli_query(
                $koneksi,
                "UPDATE users SET gaji_bulanan='$gaji_baru' WHERE id_user='$id_user'"
            );

            $_SESSION['gaji_bulanan'] = $gaji_baru;
            $gaji_sekarang = $gaji_baru;
            $success = "Gaji bulanan berhasil diperbarui.";
        }
    }
}

render_header("Profil & Gaji", "profil");
?>

<section class="grid grid-2">
    <div class="grid">
        <div class="card">
            <h2>Data Pengguna</h2>
            <br>
            <div class="grid">
                <div class="metric">
                    <span class="label">Nama</span>
                    <span class="value"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                </div>
                <div class="metric">
                    <span class="label">Username</span>
                    <span class="value"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                </div>
                <div class="metric">
                    <span class="label">Gaji bulanan saat ini</span>
                    <span class="value"><?php echo rupiah($gaji_sekarang); ?></span>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>Ubah Gaji</h2>
            <p class="muted">Gunakan tambah jika ada kenaikan gaji atau penghasilan tambahan. Gunakan kurangi jika pendapatan berkurang.</p>

            <?php if (isset($error)) { ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php } ?>

            <?php if (isset($success)) { ?>
                <p><span class="badge success"><?php echo htmlspecialchars($success); ?></span></p>
            <?php } ?>

            <form method="POST">
                <label>Jenis perubahan
                    <select name="mode" required>
                        <option value="set">Tetapkan gaji baru</option>
                        <option value="tambah">Tambah gaji/penghasilan</option>
                        <option value="kurangi">Kurangi gaji</option>
                    </select>
                </label>
                <label>Nominal
                    <input type="number" name="nominal" min="1" required>
                </label>
                <div class="actions">
                    <button name="simpan_gaji">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Ubah Profil Akun</h2>
        <p class="muted">Perbarui nama, username, atau password. Kosongkan password jika tidak ingin menggantinya.</p>

        <?php if (isset($error_profil)) { ?>
            <div class="alert"><?php echo htmlspecialchars($error_profil); ?></div>
        <?php } ?>

        <?php if (isset($success_profil)) { ?>
            <p><span class="badge success"><?php echo htmlspecialchars($success_profil); ?></span></p>
        <?php } ?>

        <form method="POST">
            <label>Nama
                <input type="text" name="nama" value="<?php echo htmlspecialchars($_SESSION['nama']); ?>" required>
            </label>
            <label>Username
                <input type="text" name="username" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>
            </label>
            <label>Password baru
                <input type="password" name="password_baru" autocomplete="new-password">
            </label>
            <label>Konfirmasi password baru
                <input type="password" name="konfirmasi_password" autocomplete="new-password">
            </label>
            <div class="actions">
                <button name="simpan_profil">Simpan Profil</button>
            </div>
        </form>
    </div>
</section>

<?php render_footer(); ?>
