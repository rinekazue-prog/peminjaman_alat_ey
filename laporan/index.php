<?php
// laporan semua peminjaman untuk petugas. cetak pakai print bawaan browser
session_start();
// hanya petugas, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// kata kunci pencarian (peminjam / alat / status). laporan tidak dipaginasi supaya cetakannya lengkap
$q = trim($_GET['q'] ?? '');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan peminjaman | Peminjaman Alat</title>
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
    <a href="../index.php" class="back no-print">Kembali</a>

    <div class="page-head">
        <h1>Laporan peminjaman</h1>
        <!-- no-print: tombol ini tidak ikut tercetak -->
        <button onclick="window.print()" class="btn btn-primary no-print">Cetak laporan</button>
    </div>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar no-print">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari nama peminjam, alat, atau status..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tgl pinjam</th>
                <th>Jatuh tempo</th>
                <th>Status</th>
                <th class="num">Denda</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            // semua peminjaman, urut tgl pinjam terbaru
            $sql = "SELECT p.*, u.username, a.nama AS alat
                    FROM peminjaman p
                    JOIN users u ON p.peminjam_id = u.id
                    JOIN alat a ON p.alat_id = a.id";
            if($q !== '') {
                $sql .= " WHERE u.username LIKE ? OR a.nama LIKE ? OR p.status LIKE ?";
            }
            $sql .= " ORDER BY p.tgl_pinjam DESC";
            $stmt = $conn->prepare($sql);
            if($q !== '') {
                $like = '%' . $q . '%';
                $stmt->bind_param('sss', $like, $like, $like);
            }
            $stmt->execute();
            $data = $stmt->get_result();
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($r['username']) ?></td>
                <td><?= e($r['alat']) ?></td>
                <!-- tanggal tampil dd/mm/yyyy -->
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <!-- warna badge ikut nama status (lihat tampil.css) -->
                <td><span class="badge <?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                <!-- format rupiah, strip kalau tidak ada denda -->
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($no == 1): ?>
            <tr><td colspan="7" class="kosong"><?= $q !== '' ? 'Data yang dicari tidak ditemukan.' : 'Belum ada data peminjaman.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
