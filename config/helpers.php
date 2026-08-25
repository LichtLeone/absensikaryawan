<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_karyawan_login() {
    if (!isset($_SESSION['karyawan_id'])) {
        header('Location: /absensi-app/login.php');
        exit;
    }
}

function require_admin_login() {
    if (!isset($_SESSION['admin_id'])) {
        header('Location: /absensi-app/login.php');
        exit;
    }
}

function base_url() {
    // Sesuaikan jika nama folder project di htdocs berbeda dari 'absensi-app'
    $protocol = (!empty($_SERVER['HTTPS'])) ? 'https' : 'http';
    return $protocol . '://' . $_SERVER['HTTP_HOST'] . '/absensi-app';
}
