<?php
// log aktivitas (admin). sebagian dicatat otomatis trigger db, sebagian dari proses_terima.php
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// pencarian (?q=...) cari nama user atau isi aktivitas
$q = trim($_GET['q'] ?? '');

// LEFT JOIN: log tetap muncul walau user-nya sudah dihapus
$sql = "SELECT log.*, users.username
        FROM log_aktifitas log
        LEFT JOIN users ON log.user_id = users.id";
if($q !== '') {
    $sql .= " WHERE users.username LIKE ? OR log.aksi LIKE ?";
}
$sql .= " ORDER BY log.waktu DESC, log.id DESC";

$tipe = ''; $params = [];
if($q !== '') {
    $like = '%' . $q . '%';
    $tipe = 'ss';
    $params = [$like, $like];
}
[$data, $page, $pages, $total, $offset] = paginate($conn, $sql, $tipe, $params);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log aktivitas | Peminjaman Alat</title>
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
        <h1>Log aktivitas</h1>
    </div>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari user atau aktivitas..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Aktivitas</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $offset + 1;
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($r['username'] ?? '-') ?></td>
                <td><?= e($r['aksi']) ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['waktu'])) ?></td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($total == 0): ?>
            <tr><td colspan="4" class="kosong"><?= $q !== '' ? 'Aktivitas yang dicari tidak ditemukan.' : 'Belum ada aktivitas.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php tampil_halaman($page, $pages); ?>
</main>
</body>
</html>
