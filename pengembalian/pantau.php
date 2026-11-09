<?php
// petugas memantau semua pengembalian (peminjam, alat, tanggal, denda). hanya lihat, tidak ada aksi
session_start();
// hanya petugas, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// pengembalian + peminjam + alat (JOIN 3 tabel)
$data = $conn->query("SELECT pg.*, p.tgl_pinjam, p.tgl_jatuh_tempo, 
                             u.username, a.nama AS alat
                      FROM pengembalian pg
                      JOIN peminjaman p ON pg.peminjaman_id = p.id
                      JOIN users u ON p.peminjam_id = u.id
                      JOIN alat a ON p.alat_id = a.id
                      ORDER BY pg.tgl_kembali DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantau pengembalian | Peminjaman Alat</title>
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
        <h1>Pantau pengembalian</h1>
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
                    <th>Tgl kembali</th>
                    <th class="num">Denda</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while($r = $data->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $r['username'] ?></td>
                    <td><?= $r['alat'] ?></td>
                    <!-- tanggal tampil dd/mm/yyyy -->
                    <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></td>
                    <!-- format rupiah, strip kalau tidak ada denda -->
                    <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <!-- belum ada data -->
    <?php else: ?>
        <p class="empty">Belum ada pengembalian.</p>
    <?php endif; ?>
</main>
</body>
</html>
