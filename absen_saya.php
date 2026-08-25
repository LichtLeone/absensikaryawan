<?php
require_once 'config/db.php';
require_once 'config/helpers.php';
require_karyawan_login();

$karyawanId = $_SESSION['karyawan_id'];

$stmt = $pdo->prepare("SELECT * FROM karyawan WHERE id = ?");
$stmt->execute([$karyawanId]);
$karyawan = $stmt->fetch();

$today = date('Y-m-d');
$stmt = $pdo->prepare("SELECT * FROM absensi WHERE karyawan_id = ? AND tanggal = ?");
$stmt->execute([$karyawanId, $today]);
$absenHariIni = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM absensi WHERE karyawan_id = ? ORDER BY tanggal DESC LIMIT 10");
$stmt->execute([$karyawanId]);
$riwayat = $stmt->fetchAll();

$sudahMasuk = $absenHariIni && $absenHariIni['jam_masuk'];
$sudahKeluar = $absenHariIni && $absenHariIni['jam_keluar'];

if (!$sudahMasuk) {
    $qrMode = 'masuk';
    $qrHeading = 'Scan untuk Absen Masuk';
} elseif ($sudahMasuk && !$sudahKeluar) {
    $qrMode = 'keluar';
    $qrHeading = 'Scan untuk Absen Keluar';
} else {
    $qrMode = 'selesai';
}

function badgeClass($status) {
    switch ($status) {
        case 'Hadir': return 'badge-hadir';
        case 'Telat': return 'badge-telat';
        default: return 'badge-alpha';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Absen Saya</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="topbar">
    <div class="brand">Absensi Karyawan</div>
    <div>
        <span style="margin-right:14px;"><?= htmlspecialchars($karyawan['nama']) ?></span>
        <a href="logout.php">Keluar</a>
    </div>
</div>

<div class="container">
    <div class="absen-grid">
        <div class="card qr-card">
            <?php if ($qrMode === 'selesai'): ?>
                <h3>Absen Hari Ini Selesai</h3>
                <p style="margin-top:10px; color:#374151; font-size:14px;">
                    Kamu sudah absen masuk (<?= substr($absenHariIni['jam_masuk'], 0, 5) ?>)
                    dan keluar (<?= substr($absenHariIni['jam_keluar'], 0, 5) ?>) hari ini.<br>
                    Sampai jumpa besok!
                </p>
            <?php else: ?>
                <h3 id="qr-heading"><?= $qrHeading ?></h3>
                <div id="qr-box"><img id="qr-img" src="" alt="QR Code"></div>
                <div class="qr-timer">Refresh dalam <span id="countdown">30</span> detik</div>
                <div class="qr-status waiting" id="qr-status">Menunggu scan...</div>
            <?php endif; ?>
        </div>

        <div>
            <div class="info-row">
                <div class="info-box">
                    <div class="label">NIS</div>
                    <div class="value"><?= htmlspecialchars($karyawan['nis']) ?></div>
                </div>
                <div class="info-box">
                    <div class="label">Jam Sekarang</div>
                    <div class="value" id="jam-sekarang">--:--:--</div>
                </div>
                <div class="info-box">
                    <div class="label">Jam Masuk</div>
                    <div class="value"><?= ($absenHariIni && $absenHariIni['jam_masuk']) ? substr($absenHariIni['jam_masuk'], 0, 5) : '-' ?></div>
                </div>
                <div class="info-box">
                    <div class="label">Jam Keluar</div>
                    <div class="value"><?= ($absenHariIni && $absenHariIni['jam_keluar']) ? substr($absenHariIni['jam_keluar'], 0, 5) : '-' ?></div>
                </div>
            </div>

            <div class="card">
                <h3 style="margin-bottom:14px;">Riwayat Absensi</h3>
                <table>
                    <thead>
                        <tr><th>NIS</th><th>Tanggal</th><th>Masuk</th><th>Keluar</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php if (empty($riwayat)): ?>
                            <tr><td colspan="5">Belum ada riwayat absensi.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($riwayat as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($karyawan['nis']) ?></td>
                                <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
                                <td><?= $r['jam_masuk'] ? substr($r['jam_masuk'], 0, 5) : '-' ?></td>
                                <td><?= $r['jam_keluar'] ? substr($r['jam_keluar'], 0, 5) : '-' ?></td>
                                <td><span class="badge <?= badgeClass($r['status']) ?>"><?= $r['status'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php if ($qrMode !== 'selesai'): ?>
<script src="assets/js/absen.js"></script>
<?php endif; ?>
</body>
</html>
