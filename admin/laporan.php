<?php
require_once '../config/db.php';
require_once '../config/helpers.php';
require_admin_login();

$dari = $_GET['dari'] ?? date('Y-m-01');
$sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT a.*, k.nama, k.nis FROM absensi a
    JOIN karyawan k ON k.id = a.karyawan_id
    WHERE a.tanggal BETWEEN ? AND ?
    ORDER BY a.tanggal DESC, k.nama ASC
");
$stmt->execute([$dari, $sampai]);
$data = $stmt->fetchAll();

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
<title>Laporan Absensi</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
@media print {
    .topbar, .no-print { display: none !important; }
}
</style>
</head>
<body>
<div class="topbar no-print">
    <div class="brand">Absensi Karyawan - Admin</div>
    <div>
        <a href="dashboard.php" style="margin-right:14px;">Dashboard</a>
        <a href="karyawan.php" style="margin-right:14px;">Data Karyawan</a>
        <a href="laporan.php" style="margin-right:14px;">Laporan</a>
        <a href="../logout.php">Keluar</a>
    </div>
</div>

<div class="container">
    <div class="page-header no-print">
        <h2>Laporan Absensi</h2>
    </div>

    <form method="GET" class="form-inline no-print" style="margin-bottom:20px;">
        <div>
            <label>Dari Tanggal</label><br>
            <input type="date" name="dari" value="<?= htmlspecialchars($dari) ?>">
        </div>
        <div>
            <label>Sampai Tanggal</label><br>
            <input type="date" name="sampai" value="<?= htmlspecialchars($sampai) ?>">
        </div>
        <div>
            <button type="submit" class="btn">Filter</button>
            <button type="button" class="btn btn-secondary" onclick="window.print()">Cetak</button>
        </div>
    </form>

    <div class="card">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Nama</th><th>NIS</th><th>Masuk</th><th>Keluar</th><th>Status</th></tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr><td colspan="6">Tidak ada data pada rentang tanggal ini.</td></tr>
                <?php endif; ?>
                <?php foreach ($data as $r): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($r['tanggal'])) ?></td>
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
