<?php
require_once '../config/db.php';
require_once '../config/helpers.php';
require_admin_login();

$today = date('Y-m-d');

$totalKaryawan = $pdo->query("SELECT COUNT(*) AS n FROM karyawan")->fetch()['n'];
$hadirHariIni = $pdo->prepare("SELECT COUNT(*) AS n FROM absensi WHERE tanggal = ?");
$hadirHariIni->execute([$today]);
$hadirHariIni = $hadirHariIni->fetch()['n'];

$stmt = $pdo->prepare("
    SELECT a.*, k.nama, k.nis FROM absensi a
    JOIN karyawan k ON k.id = a.karyawan_id
    WHERE a.tanggal = ?
    ORDER BY a.jam_masuk DESC
");
$stmt->execute([$today]);
$absenHariIni = $stmt->fetchAll();

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
<title>Dashboard Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="topbar">
    <div class="brand">Absensi Karyawan - Admin</div>
    <div>
        <a href="dashboard.php" style="margin-right:14px;">Dashboard</a>
        <a href="karyawan.php" style="margin-right:14px;">Data Karyawan</a>
        <a href="laporan.php" style="margin-right:14px;">Laporan</a>
        <a href="../logout.php">Keluar</a>
    </div>
</div>

<div class="container">
    <div class="info-row">
        <div class="info-box">
            <div class="label">Total Karyawan</div>
            <div class="value"><?= $totalKaryawan ?></div>
        </div>
        <div class="info-box">
            <div class="label">Hadir Hari Ini</div>
            <div class="value"><?= $hadirHariIni ?></div>
        </div>
        <div class="info-box">
            <div class="label">Tanggal</div>
            <div class="value" style="font-size:16px;"><?= date('d M Y') ?></div>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-bottom:14px;">Data Absensi Hari Ini</h3>
        <table>
            <thead>
                <tr><th>Nama</th><th>NIS</th><th>Masuk</th><th>Keluar</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php if (empty($absenHariIni)): ?>
                    <tr><td colspan="5">Belum ada karyawan yang absen hari ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($absenHariIni as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['nama']) ?></td>
                        <td><?= htmlspecialchars($r['nis']) ?></td>
                        <td><?= $r['jam_masuk'] ? substr($r['jam_masuk'], 0, 5) : '-' ?></td>
                        <td><?= $r['jam_keluar'] ? substr($r['jam_keluar'], 0, 5) : '-' ?></td>
                        <td><span class="badge <?= badgeClass($r['status']) ?>"><?= $r['status'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
