<?php
require_once '../config/db.php';
require_once '../config/helpers.php';
require_karyawan_login();

header('Content-Type: application/json');

$karyawanId = $_SESSION['karyawan_id'];

// Non-aktifkan token lama milik karyawan ini yang masih 'aktif'
$stmt = $pdo->prepare("UPDATE qr_token SET status = 'expired' WHERE karyawan_id = ? AND status = 'aktif'");
$stmt->execute([$karyawanId]);

// Buat token baru, berlaku 30 detik
$token = bin2hex(random_bytes(20));
$expiresAt = date('Y-m-d H:i:s', time() + 30);

$stmt = $pdo->prepare("INSERT INTO qr_token (karyawan_id, token, status, expires_at) VALUES (?, ?, 'aktif', ?)");
$stmt->execute([$karyawanId, $token, $expiresAt]);

$scanUrl = base_url() . '/scan.php?token=' . $token;

echo json_encode([
    'token' => $token,
    'scan_url' => $scanUrl,
    'expires_in' => 30,
]);
