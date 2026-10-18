<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan peminjaman | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css">
</head>
<body>
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
        <button onclick="window.print()" class="btn btn-primary no-print">Cetak laporan</button>
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
            $data = $conn->query("SELECT p.*, u.username, a.nama AS alat 
                                  FROM peminjaman p 
                                  JOIN users u ON p.peminjam_id = u.id 
                                  JOIN alat a ON p.alat_id = a.id 
                                  ORDER BY p.tgl_pinjam DESC");
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['username'] ?></td>
                <td><?= $r['alat'] ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <td><span class="badge <?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if($no == 1): ?>
            <tr><td colspan="7" class="kosong">Belum ada data peminjaman.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
