<?php
require_once '../config/db.php';
require_once '../config/helpers.php';
require_karyawan_login();

header('Content-Type: application/json');

$token = $_GET['token'] ?? '';
$karyawanId = $_SESSION['karyawan_id'];

$stmt = $pdo->prepare("SELECT status FROM qr_token WHERE token = ? AND karyawan_id = ?");
$stmt->execute([$token, $karyawanId]);
$row = $stmt->fetch();

if (!$row) {
    echo json_encode(['status' => 'invalid']);
    exit;
}

echo json_encode(['status' => $row['status']]); // aktif | terpakai | expired
