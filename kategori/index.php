<?php
// daftar kategori alat (admin)
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php'); exit;
}
include '../config/connection.php';

// pencarian (nama / deskripsi kategori)
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM kategori";
$tipe = ''; $params = [];
if($q !== '') {
    $sql .= " WHERE nama LIKE ? OR deskripsi LIKE ?";
    $like = '%' . $q . '%';
    $tipe = 'ss';
    $params = [$like, $like];
}
$sql .= " ORDER BY id DESC";
[$data, $page, $pages, $total, $offset] = paginate($conn, $sql, $tipe, $params);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data kategori | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css?v=2">
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
        <h1>Data kategori</h1>
        <a href="tambah.php" class="btn btn-primary">Tambah kategori</a>
    </div>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari nama atau deskripsi kategori..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th class="aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $offset + 1;
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['deskripsi']) ?></td>
                <!-- confirm() supaya tidak kepencet hapus -->
                <td class="aksi">
                    <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                    <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($total == 0): ?>
            <tr><td colspan="4" class="kosong"><?= $q !== '' ? 'Kategori yang dicari tidak ditemukan.' : 'Belum ada kategori.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php tampil_halaman($page, $pages); ?>
</main>
</body>
</html>
