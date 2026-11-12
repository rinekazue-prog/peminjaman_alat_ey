<?php
// daftar semua akun beserta rolenya (admin)
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// pencarian (?q=...) cari username
$q = trim($_GET['q'] ?? '');
$sql = "SELECT * FROM users";
$tipe = ''; $params = [];
if($q !== '') {
    $sql .= " WHERE username LIKE ?";
    $tipe = 's';
    $params = ['%' . $q . '%'];
}
$sql .= " ORDER BY id DESC";
[$data, $page, $pages, $total, $offset] = paginate($conn, $sql, $tipe, $params);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data user | Peminjaman Alat</title>
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
        <h1>Data user</h1>
        <a href="tambah.php" class="btn btn-primary">Tambah user</a>
    </div>

    <!-- flash error: tampil sekali, langsung di-unset -->
    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari username..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Username</th>
                <th>Role</th>
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
                <td><?= e($r['username']) ?></td>
                <td><?= ucfirst($r['role']) ?></td>
                <!-- confirm() supaya tidak kepencet hapus -->
                <td class="aksi">
                    <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                    <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus user ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($total == 0): ?>
            <tr><td colspan="4" class="kosong"><?= $q !== '' ? 'User yang dicari tidak ditemukan.' : 'Belum ada user.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php tampil_halaman($page, $pages); ?>
</main>
</body>
</html>
