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
$data = $conn->query("SELECT p.*, u.username, a.nama AS alat
                       FROM peminjaman p
                       JOIN users u ON p.peminjam_id = u.id
                       JOIN alat a ON p.alat_id = a.id
                       WHERE p.status = 'menunggu_konfirmasi'
                       ORDER BY p.tgl_ajukan_kembali ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima pengembalian | Peminjaman Alat</title>
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
        <h1>Terima pengembalian</h1>
    </div>

    <!-- notif sukses (dari ?status=success) -->
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <p class="notice notice-ok" role="status">Pengembalian alat berhasil dikonfirmasi diterima dan dicatat.</p>
    <?php endif; ?>

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
                $no = 1;
                while($r = $data->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $r['username'] ?></td>
                    <td><?= $r['alat'] ?></td>
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
    <!-- belum ada data -->
    <?php else: ?>
        <p class="empty">Tidak ada pengajuan pengembalian yang menunggu konfirmasi.</p>
    <?php endif; ?>
</main>
</body>
</html>
