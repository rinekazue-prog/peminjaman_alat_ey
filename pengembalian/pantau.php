<?php
// petugas memantau semua pengembalian (peminjam, alat, tanggal, denda). hanya lihat, tidak ada aksi
session_start();
// hanya petugas, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// kata kunci pencarian (peminjam / alat)
$q = trim($_GET['q'] ?? '');

// pengembalian + peminjam + alat (JOIN 3 tabel)
$sql = "SELECT pg.*, p.tgl_pinjam, p.tgl_jatuh_tempo,
               u.username, a.nama AS alat
        FROM pengembalian pg
        JOIN peminjaman p ON pg.peminjaman_id = p.id
        JOIN users u ON p.peminjam_id = u.id
        JOIN alat a ON p.alat_id = a.id";
$tipe = ''; $params = [];
if($q !== '') {
    $sql .= " WHERE u.username LIKE ? OR a.nama LIKE ?";
    $like = '%' . $q . '%';
    $tipe = 'ss';
    $params = [$like, $like];
}
$sql .= " ORDER BY pg.tgl_kembali DESC";
[$data, $page, $pages, $total, $offset] = paginate($conn, $sql, $tipe, $params);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantau pengembalian | Peminjaman Alat</title>
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
        <h1>Pantau pengembalian</h1>
    </div>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari nama peminjam atau alat..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Alat</th>
                    <th>Tgl pinjam</th>
                    <th>Tgl kembali</th>
                    <th class="num">Denda</th>
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
                    <td><?= e($r['alat']) ?></td>
                    <!-- tanggal tampil dd/mm/yyyy -->
                    <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></td>
                    <!-- format rupiah, strip kalau tidak ada denda -->
                    <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
                </tr>
                <?php endwhile; ?>
            <?php if($total == 0): ?>
                <tr><td colspan="6" class="kosong"><?= $q !== '' ? 'Pengembalian yang dicari tidak ditemukan.' : 'Belum ada pengembalian.' ?></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    <?php tampil_halaman($page, $pages); ?>
</main>
</body>
</html>
