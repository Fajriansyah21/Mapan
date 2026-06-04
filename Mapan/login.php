<?php
session_start();
include "koneksi.php";

if(isset($_POST['login'])){
    $username = htmlspecialchars(trim($_POST['username']));
    $password = htmlspecialchars(trim($_POST['password']));

    if(empty($username) || empty($password)){
        $error = "Username dan password wajib diisi!";
    } else {
        $query = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' AND password='$password'");
        $data = mysqli_fetch_assoc($query);

        if($data){
            $_SESSION['id_user'] = $data['id_user'];
            $_SESSION['nama'] = $data['nama'];
            $_SESSION['username'] = $data['username'];
            $_SESSION['gaji_bulanan'] = $data['gaji_bulanan'];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Login gagal! Username atau password salah.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login BudgetKu</title>
</head>
<body style="font-family: Arial;">

<h2>Login BudgetKu</h2>

<?php
if(isset($error)){
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="POST" action="">
    <label>Username</label><br>
    <input type="text" name="username"><br><br>

    <label>Password</label><br>
    <input type="password" name="password"><br><br>

    <button type="submit" name="login">Login</button>
</form>

</body>
</html>
