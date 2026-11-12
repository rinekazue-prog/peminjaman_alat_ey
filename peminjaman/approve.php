<?php
// daftar pengajuan pending buat petugas: bisa dicari, ada info stok, tombol setujui / tolak
session_start();
// hanya petugas
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// token anti-CSRF: dibuat sekali per sesi, ikut dikirim di setiap form setujui/tolak
if(empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}

// kata kunci pencarian (peminjam / alat)
$q = trim($_GET['q'] ?? '');

// pengajuan pending + nama peminjam, alat & sisa stok (JOIN)
$sql = "SELECT p.*, u.username, a.nama AS alat, a.stok
        FROM peminjaman p
        JOIN users u ON p.peminjam_id = u.id
        JOIN alat a ON p.alat_id = a.id
        WHERE p.status = 'pending'";
if($q !== '') {
    $sql .= " AND (u.username LIKE ? OR a.nama LIKE ?)";
}
// yang paling lama menunggu tampil paling atas (antrian)
$sql .= " ORDER BY p.id ASC";

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
    <title>Setujui peminjaman | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css?v=2">
</head>
<body>
<!-- topbar -->
<header class="topbar">
    <a href="../index.php" class="brand">Peminjaman Alat</a>
    <div class="user">
        <span><?= e($_SESSION['username']) ?></span>
        <a href="../logout.php">Keluar</a>
    </div>
</header>

<main class="page">
    <a href="index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Setujui peminjaman</h1>
    </div>

    <!-- flash notif: tampil sekali, langsung di-unset -->
    <?php if(isset($_SESSION['success'])): ?>
        <p class="notice notice-ok" role="status"><?= e($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>
    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari peminjam atau alat..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th class="num">Stok</th>
                <th>Tgl pinjam</th>
                <th>Jatuh tempo</th>
                <th class="aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $offset + 1;
            while($r = $data->fetch_assoc()):
                // stok habis = tidak bisa disetujui (hanya bisa ditolak)
                $stok_ada = $r['stok'] > 0;
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($r['username']) ?></td>
                <td><?= e($r['alat']) ?></td>
                <td class="num"><?= $stok_ada ? (int) $r['stok'] : '<span class="badge habis">Habis</span>' ?></td>
                <!-- tanggal tampil dd/mm/yyyy -->
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <td class="aksi">
                    <!-- aksi mengubah data, jadi lewat POST (bukan link GET) + token csrf.
                         form-nya kosong & tersembunyi, tombol tersambung lewat atribut form="" -->
                    <form method="post" action="proses_approve.php" id="f<?= $r['id'] ?>" hidden>
                        <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    </form>
                    <button type="submit" form="f<?= $r['id'] ?>" name="aksi" value="setujui" class="btn btn-primary btn-sm" <?= $stok_ada ? '' : 'disabled title="Stok habis"' ?>
                            onclick="return confirm('Setujui peminjaman ini?')">Setujui</button>
                    <button type="submit" form="f<?= $r['id'] ?>" name="aksi" value="tolak" class="btn btn-danger btn-sm"
                            onclick="return confirm('Tolak peminjaman ini?')">Tolak</button>
                </td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($total == 0): ?>
            <tr><td colspan="7" class="kosong"><?= $q !== '' ? 'Pengajuan yang dicari tidak ditemukan.' : 'Tidak ada pengajuan yang menunggu persetujuan.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php tampil_halaman($page, $pages); ?>
</main>
</body>
</html>
