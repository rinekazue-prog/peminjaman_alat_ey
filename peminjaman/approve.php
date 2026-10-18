<?php
session_start();
// HANYA PETUGAS yang boleh akses
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
    <title>Setujui peminjaman | Peminjaman Alat</title>
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
    <a href="index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Setujui peminjaman</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tgl pinjam</th>
                <th>Jatuh tempo</th>
                <th class="aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $data = $conn->query("SELECT p.*, u.username, a.nama AS alat 
                                  FROM peminjaman p 
                                  JOIN users u ON p.peminjam_id = u.id 
                                  JOIN alat a ON p.alat_id = a.id 
                                  WHERE p.status = 'pending'
                                  ORDER BY p.id DESC");
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['username'] ?></td>
                <td><?= $r['alat'] ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <td class="aksi">
                    <a href="proses_approve.php?id=<?= $r['id'] ?>&aksi=setujui" class="btn btn-primary btn-sm" onclick="return confirm('Setujui peminjaman ini?')">Setujui</a>
                    <a href="proses_approve.php?id=<?= $r['id'] ?>&aksi=tolak" class="btn btn-danger btn-sm" onclick="return confirm('Tolak peminjaman ini?')">Tolak</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if($no == 1): ?>
            <tr><td colspan="6" class="kosong">Tidak ada pengajuan yang menunggu persetujuan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
