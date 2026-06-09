<?php
session_start();
include "koneksi.php";

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    if (empty($username) || empty($password)) {
        $error = "Username dan password wajib diisi.";
    } else {
        $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
        $data = mysqli_fetch_assoc($query);

        if ($data) {
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['gaji_bulanan'] = $data['gaji_bulanan'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Login gagal. Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Mapan</title>
    <link rel="stylesheet" href="Assets/style.css">
</head>
<body class="login-page">
    <div class="login-box">
        <div class="brand" style="color:#172033; margin-bottom:20px;">
            <img src="Assets/logo.png" alt="Logo Mapan">
            <div>
                <strong>Mapan</strong>
                <span style="color:#667085;">Simulator Anggaran Bulanan</span>
            </div>
        </div>

        <?php if (isset($error)) { ?>
            <div class="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form method="POST">
            <label>Username
                <input type="text" name="username" required>
            </label>
            <label>Password
                <input type="password" name="password" required>
            </label>
            <button type="submit" name="login">Login</button>
        </form>
    </div>
</body>
</html>
