<?php
// daftar pengajuan pengembalian yang menunggu konfirmasi petugas
session_start();
// hanya petugas
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// pengembalian yang sudah diajukan peminjam,
// tapi belum dikonfirmasi petugas, urut dari yang paling lama menunggu
$q = trim($_GET['q'] ?? '');
$sql = "SELECT p.*, u.username, a.nama AS alat
        FROM peminjaman p
        JOIN users u ON p.peminjam_id = u.id
        JOIN alat a ON p.alat_id = a.id
        WHERE p.status = 'menunggu_konfirmasi'";
if($q !== '') { $sql .= " AND (u.username LIKE ? OR a.nama LIKE ?)"; }
$sql .= " ORDER BY p.tgl_ajukan_kembali ASC";
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
    <title>Terima pengembalian | Peminjaman Alat</title>
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
        <h1>Terima pengembalian</h1>
    </div>

    <!-- notif sukses (dari ?status=success) -->
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <p class="notice notice-ok" role="status">Pengembalian alat berhasil dikonfirmasi diterima dan dicatat.</p>
    <?php endif; ?>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari peminjam atau alat..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <!-- tabel cuma tampil kalau ada data -->
    <?php if($data->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Alat</th>
                    <th>Tgl pinjam</th>
                    <th>Diajukan kembali</th>
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
                    <td><?= e($r['alat']) ?></td>
                    <!-- tanggal tampil dd/mm/yyyy -->
                    <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                    <td><?= $r['tgl_ajukan_kembali'] ? date('d/m/Y', strtotime($r['tgl_ajukan_kembali'])) : '-' ?></td>
                    <td class="aksi">
                        <!-- id peminjaman dikirim lewat URL -->
                        <a href="proses_terima.php?id=<?= $r['id'] ?>"
                           class="btn btn-primary btn-sm"
                           onclick="return confirm('Konfirmasi alat ini sudah diterima secara fisik dari peminjam?')">Konfirmasi diterima</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <?php tampil_halaman($page, $pages); ?>
    <!-- belum ada data -->
    <?php else: ?>
        <p class="empty"><?= $q !== '' ? 'Pengajuan yang dicari tidak ditemukan.' : 'Tidak ada pengajuan pengembalian yang menunggu konfirmasi.' ?></p>
    <?php endif; ?>
</main>
</body>
</html>
