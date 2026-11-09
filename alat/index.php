<?php
// daftar alat + status Tersedia/Habis (dilihat dari stok)
// tombol tambah/edit/hapus cuma muncul untuk admin
session_start();
// wajib login
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
// role dipakai buat nampilin/menyembunyikan tombol admin
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar alat | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css">
</head>
<body>
<!-- topbar -->
<header class="topbar">
    <a href="../index.php" class="brand">Peminjaman Alat</a>
    <div class="user">
        <span><?= $_SESSION['username'] ?></span>
        <a href="../logout.php">Keluar</a>
    </div>
</header>

<main class="page">
    <a href="../index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Daftar alat</h1>
        <?php if($role == 'admin'): ?>
            <!-- cuma admin yang boleh tambah -->
            <a href="tambah.php" class="btn btn-primary">Tambah alat</a>
        <?php endif; ?>
    </div>

    <!-- flash error: tampil sekali, langsung di-unset -->
    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama alat</th>
                <th>Kategori</th>
                <th class="num">Stok</th>
                <th>Status</th>
                <?php if($role == 'admin'): ?>
                    <th class="aksi">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // alat + nama kategori (LEFT JOIN, alat tanpa kategori tetap muncul)
            $data = $conn->query("SELECT alat.*, kategori.nama AS kat FROM alat LEFT JOIN kategori ON alat.kategori_id=kategori.id ORDER BY alat.id DESC");
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['nama'] ?></td>
                <td><?= $r['kat'] ?></td>
                <td class="num"><?= $r['stok'] ?></td>
                <!-- stok > 0 = Tersedia, selain itu Habis -->
                <td><span class="badge <?= $r['stok'] > 0 ? 'tersedia' : 'habis' ?>"><?= $r['stok'] > 0 ? 'Tersedia' : 'Habis' ?></span></td>
                <?php if($role == 'admin'): ?>
                    <!-- confirm() supaya tidak kepencet hapus -->
                    <td class="aksi">
                        <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                        <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus alat ini?')">Hapus</a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($no == 1): ?>
            <tr><td colspan="<?= $role == 'admin' ? 6 : 5 ?>" class="kosong">Belum ada alat.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>