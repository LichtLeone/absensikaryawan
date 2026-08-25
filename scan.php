<?php
$token = $_GET['token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Absen - Masukkan NIS</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="scan-wrapper">
    <div class="auth-card" style="max-width: 100%;">
        <h2>Konfirmasi Absen</h2>
        <p class="subtitle">Masukkan NIS kamu untuk menyelesaikan absensi</p>

        <div id="result-box"></div>

        <?php if (!$token): ?>
            <p class="alert alert-error">Token tidak ditemukan. Scan ulang QR dari layar komputer.</p>
        <?php else: ?>
            <form id="form-nis">
                <input type="hidden" id="token" value="<?= htmlspecialchars($token) ?>">
                <label>NIS</label>
                <input type="text" id="nis" name="nis" inputmode="numeric" required autofocus>
                <button type="submit" class="btn">Konfirmasi Absen</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<script src="assets/js/scan.js"></script>
</body>
</html>
