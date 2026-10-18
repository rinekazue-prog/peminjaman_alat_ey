<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header('location: login.php');
    exit;
}
include 'config/connection.php';
$user_id = (int) $_SESSION['id'];

// Riwayat lengkap: semua peminjaman milik user ini + tgl kembali (kalau sudah dikembalikan)
$stmt = $conn->prepare("SELECT p.*, a.nama AS alat, pg.tgl_kembali, pg.denda AS denda_kembali
                        FROM peminjaman p
                        JOIN alat a ON p.alat_id = a.id
                        LEFT JOIN pengembalian pg ON pg.peminjaman_id = p.id
                        WHERE p.peminjam_id = ?
                        ORDER BY p.id DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$data = $stmt->get_result();

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
    <link rel="stylesheet" href="assets/css/tampil.css">
</head>
<body>
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
            $no = 1;
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['alat'] ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <td><?= $r['tgl_kembali'] ? date('d/m/Y', strtotime($r['tgl_kembali'])) : '-' ?></td>
                <td><span class="badge <?= $r['status'] ?>"><?= $label_status[$r['status']] ?? ucfirst($r['status']) ?></span></td>
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if($no == 1): ?>
            <tr><td colspan="7" class="kosong">Belum ada riwayat peminjaman.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
