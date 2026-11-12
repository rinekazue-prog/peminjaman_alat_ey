<?php
// riwayat peminjaman milik peminjam yang login, lengkap dengan tgl kembali dan denda
session_start();
// hanya peminjam, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header('location: login.php');
    exit;
}
include 'config/connection.php';
// id user yang login
$user_id = (int) $_SESSION['id'];

// kata kunci pencarian (nama alat / status)
$q = trim($_GET['q'] ?? '');

// semua peminjaman user ini. LEFT JOIN pengembalian supaya yang belum kembali tetap muncul
$sql = "SELECT p.*, a.nama AS alat, pg.tgl_kembali, pg.denda AS denda_kembali
        FROM peminjaman p
        JOIN alat a ON p.alat_id = a.id
        LEFT JOIN pengembalian pg ON pg.peminjaman_id = p.id
        WHERE p.peminjam_id = ?";
$tipe = 'i'; $params = [$user_id];
if($q !== '') {
    $sql .= " AND (a.nama LIKE ? OR p.status LIKE ?)";
    $like = '%' . $q . '%';
    $tipe .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
$sql .= " ORDER BY p.id DESC";
[$data, $page, $pages, $total, $offset] = paginate($conn, $sql, $tipe, $params);

// label status (di db namanya pakai underscore)
$label_status = [
    'pending'              => 'Pending',
    'disetujui'            => 'Disetujui',
    'menunggu_konfirmasi'  => 'Menunggu konfirmasi',
    'selesai'              => 'Selesai',
    'ditolak'              => 'Ditolak',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat peminjaman | Peminjaman Alat</title>
    <link rel="stylesheet" href="assets/css/tampil.css?v=2">
</head>
<body>
<!-- topbar -->
<header class="topbar">
    <a href="index.php" class="brand">Peminjaman Alat</a>
    <div class="user">
        <span><?= $_SESSION['username'] ?></span>
        <a href="logout.php">Keluar</a>
    </div>
</header>

<main class="page">
    <a href="index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Riwayat peminjaman</h1>
    </div>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari nama alat atau status..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Alat</th>
                <th>Tgl pinjam</th>
                <th>Jatuh tempo</th>
                <th>Tgl kembali</th>
                <th>Status</th>
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
                <td><?= e($r['alat']) ?></td>
                <!-- tanggal tampil dd/mm/yyyy -->
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <td><?= $r['tgl_kembali'] ? date('d/m/Y', strtotime($r['tgl_kembali'])) : '-' ?></td>
                <!-- warna badge ikut nama status (lihat tampil.css) -->
                <td><span class="badge <?= $r['status'] ?>"><?= $label_status[$r['status']] ?? ucfirst($r['status']) ?></span></td>
                <!-- format rupiah, strip kalau tidak ada denda -->
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($total == 0): ?>
            <tr><td colspan="7" class="kosong"><?= $q !== '' ? 'Riwayat yang dicari tidak ditemukan.' : 'Belum ada riwayat peminjaman.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php tampil_halaman($page, $pages); ?>
</main>
</body>
</html>
