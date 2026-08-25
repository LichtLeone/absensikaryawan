# Absensi Karyawan - QR Code

## Cara install (XAMPP)

1. Extract folder `absensi-app` ke dalam `C:\xampp\htdocs\` (Windows) atau `/Applications/XAMPP/htdocs/` (Mac).
2. Start **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`, buat database baru dengan cara import file `database/schema.sql` (tab Import, pilih file tersebut). Ini akan otomatis membuat database `db_absensi` beserta tabel-tabelnya.
4. Buka `http://localhost/absensi-app/install.php` di browser → buat username & password admin pertama.
5. Login di `http://localhost/absensi-app/login.php` pakai akun admin tadi.
6. Masuk ke menu **Data Karyawan** → tambahkan karyawan (nama, username, password, NIS, jabatan).
7. Logout, lalu login lagi pakai username/password karyawan yang baru dibuat → akan diarahkan ke halaman **Absen Saya** dengan QR code.

## Cara pakai (absen)

1. Karyawan login di komputer → QR muncul di kiri, auto-refresh tiap 30 detik.
2. Karyawan scan QR pakai kamera bawaan HP → browser HP terbuka otomatis ke halaman input NIS.
3. Karyawan masukkan NIS miliknya sendiri → submit.
4. Kalau NIS cocok dengan yang sedang login di komputer → absen tercatat (check-in pertama kali, check-out kalau sudah check-in sebelumnya). Halaman di komputer otomatis menampilkan "✓ Absen tercatat".
5. Kalau NIS tidak cocok atau QR sudah kedaluwarsa → ditolak dengan pesan error.

## Catatan penting

- **Wajib diakses lewat jaringan yang sama** antara komputer dan HP (misalnya WiFi rumah/kantor yang sama), supaya HP bisa membuka URL `scan.php` dari server XAMPP di komputer. Kalau diakses dari `localhost`, HP tidak akan bisa membuka halamannya — ganti akses di komputer memakai alamat IP lokal komputer, misalnya `http://192.168.1.10/absensi-app/login.php` (cek IP lewat `ipconfig` di Windows atau `ifconfig` di Mac/Linux), supaya QR yang dihasilkan berisi URL yang bisa diakses HP juga.
- Kalau nama folder project di htdocs kamu ganti dari `absensi-app`, sesuaikan juga fungsi `base_url()` di `config/helpers.php`.
- QR code dibuat lewat layanan gratis `api.qrserver.com` (butuh internet aktif di komputer & HP saat generate/scan QR). Kalau butuh full offline, bisa diganti pakai library QR lokal — bilang aja kalau mau dibantu ganti ke versi offline.
- Batas jam telat saat ini di-hardcode jam 08:00 di `api/verify.php` (variabel `$batasTelat`), bisa diubah sesuai kebutuhan.
- Status "Alpha" belum diisi otomatis (perlu proses/cron terpisah untuk tandai karyawan yang tidak absen di akhir hari) — untuk sekarang bisa diisi manual lewat phpMyAdmin kalau perlu.
