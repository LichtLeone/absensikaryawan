<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$token = $_POST['token'] ?? '';
$nis = trim($_POST['nis'] ?? '');

if (!$token || !$nis) {
    echo json_encode(['success' => false, 'message' => 'Token atau NIS tidak boleh kosong.']);
    exit;
}

// 1. Ambil data token
$stmt = $pdo->prepare("SELECT * FROM qr_token WHERE token = ?");
$stmt->execute([$token]);
$qr = $stmt->fetch();

if (!$qr) {
    echo json_encode(['success' => false, 'message' => 'QR tidak valid.']);
    exit;
}

if ($qr['status'] !== 'aktif' || strtotime($qr['expires_at']) < time()) {
    echo json_encode(['success' => false, 'message' => 'QR sudah kedaluwarsa, minta karyawan refresh QR di layar komputer.']);
    exit;
}

// 2. Cocokkan NIS dengan karyawan yang sedang generate QR ini
$stmt = $pdo->prepare("SELECT * FROM karyawan WHERE nis = ?");
$stmt->execute([$nis]);
$karyawan = $stmt->fetch();

if (!$karyawan) {
    echo json_encode(['success' => false, 'message' => 'NIS tidak ditemukan.']);
    exit;
}

if ($karyawan['id'] != $qr['karyawan_id']) {
    echo json_encode(['success' => false, 'message' => 'NIS tidak sesuai dengan sesi login QR ini.']);
    exit;
}

// 3. Cek data absensi hari ini
$today = date('Y-m-d');
$now = date('H:i:s');

$stmt = $pdo->prepare("SELECT * FROM absensi WHERE karyawan_id = ? AND tanggal = ?");
$stmt->execute([$karyawan['id'], $today]);
$absen = $stmt->fetch();

if (!$absen) {
    // Belum absen sama sekali hari ini -> Check-in
    $batasTelat = '08:00:00';
    $status = ($now > $batasTelat) ? 'Telat' : 'Hadir';

    $stmt = $pdo->prepare("INSERT INTO absensi (karyawan_id, tanggal, jam_masuk, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$karyawan['id'], $today, $now, $status]);

    $pesan = "Check-in berhasil pukul $now ($status)";
} elseif (!$absen['jam_keluar']) {
    // Sudah check-in, belum check-out -> Check-out
    $stmt = $pdo->prepare("UPDATE absensi SET jam_keluar = ? WHERE id = ?");
    $stmt->execute([$now, $absen['id']]);

    $pesan = "Check-out berhasil pukul $now";
} else {
    // Sudah check-in dan check-out hari ini
    echo json_encode(['success' => false, 'message' => 'Kamu sudah check-in dan check-out hari ini.']);
    exit;
}

// 4. Tandai token sebagai terpakai supaya tidak bisa dipakai ulang
$stmt = $pdo->prepare("UPDATE qr_token SET status = 'terpakai' WHERE id = ?");
$stmt->execute([$qr['id']]);

echo json_encode(['success' => true, 'message' => $pesan, 'nama' => $karyawan['nama']]);
