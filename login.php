<?php
require_once 'config/db.php';
require_once 'config/helpers.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Cek dulu ke tabel admin
    $stmt = $pdo->prepare("SELECT * FROM admin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        header('Location: admin/dashboard.php');
        exit;
    }

    // Kalau bukan admin, cek tabel karyawan
    $stmt = $pdo->prepare("SELECT * FROM karyawan WHERE username = ?");
    $stmt->execute([$username]);
    $karyawan = $stmt->fetch();

    if ($karyawan && password_verify($password, $karyawan['password'])) {
        $_SESSION['karyawan_id'] = $karyawan['id'];
        $_SESSION['karyawan_nama'] = $karyawan['nama'];
        header('Location: absen_saya.php');
        exit;
    }

    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Absensi Karyawan</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Login</h2>
        <p class="subtitle">Sistem Absensi Karyawan</p>

        <?php if ($error): ?>
            <p class="alert alert-error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit" class="btn">Masuk</button>
        </form>
    </div>
</div>
</body>
</html>
