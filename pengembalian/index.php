<?php
// data pengembalian untuk admin, bisa diedit/dihapus manual
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
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
    <title>Data pengembalian | Peminjaman Alat</title>
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
        <h1>Data pengembalian</h1>
        <a href="tambah.php" class="btn btn-primary">Catat pengembalian</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID peminjaman</th>
                <th>Tgl kembali</th>
                <th class="num">Denda</th>
                <th class="aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $data = $conn->query("SELECT * FROM pengembalian ORDER BY id DESC");
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['peminjaman_id'] ?></td>
                <!-- tanggal tampil dd/mm/yyyy -->
                <td><?= date('d/m/Y', strtotime($r['tgl_kembali'])) ?></td>
                <!-- format rupiah, strip kalau tidak ada denda -->
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
                <!-- confirm() supaya tidak kepencet hapus -->
                <td class="aksi">
                    <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                    <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus data pengembalian ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($no == 1): ?>
            <tr><td colspan="5" class="kosong">Belum ada pengembalian.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
