<?php
require_once '../config/db.php';
require_once '../config/helpers.php';
require_admin_login();

$msg = '';
$editKaryawan = null;

// Hapus karyawan
if (isset($_GET['hapus'])) {
    $stmt = $pdo->prepare("DELETE FROM karyawan WHERE id = ?");
    $stmt->execute([$_GET['hapus']]);
    header('Location: karyawan.php?msg=deleted');
    exit;
}

// Ambil data untuk mode edit
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM karyawan WHERE id = ?");
    $stmt->execute([$_GET['edit']]);
    $editKaryawan = $stmt->fetch();
}

// Simpan (tambah / update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $nis = trim($_POST['nis']);
    $jabatan = trim($_POST['jabatan']);
    $password = $_POST['password'];

    try {
        if ($id) {
            // Update - password opsional (kalau kosong, tidak diubah)
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE karyawan SET nama=?, username=?, nis=?, jabatan=?, password=? WHERE id=?");
                $stmt->execute([$nama, $username, $nis, $jabatan, $hash, $id]);
            } else {
                $stmt = $pdo->prepare("UPDATE karyawan SET nama=?, username=?, nis=?, jabatan=? WHERE id=?");
                $stmt->execute([$nama, $username, $nis, $jabatan, $id]);
            }
        } else {
            // Tambah baru - password wajib
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO karyawan (nama, username, password, nis, jabatan) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $username, $hash, $nis, $jabatan]);
        }
        header('Location: karyawan.php?msg=saved');
        exit;
    } catch (PDOException $e) {
        $msg = 'Gagal simpan: username atau NIS mungkin sudah dipakai.';
    }
}

$daftarKaryawan = $pdo->query("SELECT * FROM karyawan ORDER BY nama ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Data Karyawan</title>
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
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'saved'): ?>
        <p class="alert alert-success">Data karyawan berhasil disimpan.</p>
    <?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
        <p class="alert alert-success">Data karyawan berhasil dihapus.</p>
    <?php elseif ($msg): ?>
        <p class="alert alert-error"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <div class="card" style="margin-bottom:20px;">
        <h3 style="margin-bottom:14px;"><?= $editKaryawan ? 'Edit Karyawan' : 'Tambah Karyawan' ?></h3>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $editKaryawan['id'] ?? '' ?>">
            <div class="form-inline">
                <div>
                    <label>Nama</label><br>
                    <input type="text" name="nama" required value="<?= htmlspecialchars($editKaryawan['nama'] ?? '') ?>">
                </div>
                <div>
                    <label>Username</label><br>
                    <input type="text" name="username" required value="<?= htmlspecialchars($editKaryawan['username'] ?? '') ?>">
                </div>
                <div>
                    <label>NIS</label><br>
                    <input type="text" name="nis" required value="<?= htmlspecialchars($editKaryawan['nis'] ?? '') ?>">
                </div>
                <div>
                    <label>Jabatan</label><br>
                    <input type="text" name="jabatan" value="<?= htmlspecialchars($editKaryawan['jabatan'] ?? '') ?>">
                </div>
                <div>
                    <label>Password <?= $editKaryawan ? '(kosongkan jika tidak diubah)' : '' ?></label><br>
                    <input type="password" name="password" <?= $editKaryawan ? '' : 'required' ?>>
                </div>
                <div>
                    <button type="submit" class="btn"><?= $editKaryawan ? 'Update' : 'Tambah' ?></button>
                    <?php if ($editKaryawan): ?>
                        <a href="karyawan.php" class="btn btn-secondary">Batal</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>

    <div class="card">
        <h3 style="margin-bottom:14px;">Daftar Karyawan</h3>
        <table>
            <thead>
                <tr><th>Nama</th><th>Username</th><th>NIS</th><th>Jabatan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (empty($daftarKaryawan)): ?>
                    <tr><td colspan="5">Belum ada data karyawan.</td></tr>
                <?php endif; ?>
                <?php foreach ($daftarKaryawan as $k): ?>
                    <tr>
                        <td><?= htmlspecialchars($k['nama']) ?></td>
                        <td><?= htmlspecialchars($k['username']) ?></td>
                        <td><?= htmlspecialchars($k['nis']) ?></td>
                        <td><?= htmlspecialchars($k['jabatan']) ?></td>
                        <td>
                            <a href="karyawan.php?edit=<?= $k['id'] ?>">Edit</a>
                            &nbsp;|&nbsp;
                            <a href="karyawan.php?hapus=<?= $k['id'] ?>" onclick="return confirm('Yakin hapus karyawan ini?')" style="color:#DC2626;">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
