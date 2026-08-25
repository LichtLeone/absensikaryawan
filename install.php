<?php
require_once 'config/db.php';

$msg = '';
$done = false;

// Cegah dipakai berkali-kali kalau admin sudah ada
$cek = $pdo->query("SELECT COUNT(*) AS jumlah FROM admin")->fetch();
$sudahAdaAdmin = $cek['jumlah'] > 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$sudahAdaAdmin) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        $msg = "Akun admin berhasil dibuat. Silakan login.";
        $done = true;
    } else {
        $msg = "Username dan password wajib diisi.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Install - Buat Akun Admin</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Setup Awal</h2>
        <p class="subtitle">Buat akun admin pertama untuk sistem absensi</p>

        <?php if ($sudahAdaAdmin && !$done): ?>
            <p class="alert alert-info">Akun admin sudah ada. Hapus baris ini kalau mau setup ulang, atau langsung <a href="login.php">login</a>.</p>
        <?php elseif ($done): ?>
            <p class="alert alert-success"><?= htmlspecialchars($msg) ?></p>
            <a class="btn" href="login.php">Ke Halaman Login</a>
        <?php else: ?>
            <?php if ($msg): ?><p class="alert alert-error"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
            <form method="POST">
                <label>Username Admin</label>
                <input type="text" name="username" required>
                <label>Password</label>
                <input type="password" name="password" required>
                <button type="submit" class="btn">Buat Akun Admin</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
