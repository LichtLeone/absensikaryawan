-- Jalankan file ini di phpMyAdmin (Import) atau lewat tab SQL
CREATE DATABASE IF NOT EXISTS db_absensi;
USE db_absensi;

CREATE TABLE admin (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE karyawan (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    nis VARCHAR(30) UNIQUE NOT NULL,
    jabatan VARCHAR(50) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE absensi (
    id INT PRIMARY KEY AUTO_INCREMENT,
    karyawan_id INT NOT NULL,
    tanggal DATE NOT NULL,
    jam_masuk TIME DEFAULT NULL,
    jam_keluar TIME DEFAULT NULL,
    status VARCHAR(20) DEFAULT 'Hadir',
    FOREIGN KEY (karyawan_id) REFERENCES karyawan(id) ON DELETE CASCADE,
    UNIQUE KEY unique_absen_harian (karyawan_id, tanggal)
);

-- Menyimpan token QR yang sedang aktif per sesi login karyawan
CREATE TABLE qr_token (
    id INT PRIMARY KEY AUTO_INCREMENT,
    karyawan_id INT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    status ENUM('aktif','terpakai','expired') DEFAULT 'aktif',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME NOT NULL,
    FOREIGN KEY (karyawan_id) REFERENCES karyawan(id) ON DELETE CASCADE
);

-- Data contoh karyawan (password: karyawan123 -- akan di-hash lewat install.php, jangan insert manual di sini)
