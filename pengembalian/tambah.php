<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// Ambil peminjaman yang belum selesai dikembalikan (masih dipinjam / sudah diajukan tapi belum dikonfirmasi petugas)
$pjm = $conn->query("SELECT p.id, u.username, a.nama AS alat, p.tgl_pinjam, p.tgl_jatuh_tempo 
                     FROM peminjaman p 
                     JOIN users u ON p.peminjam_id = u.id 
                     JOIN alat a ON p.alat_id = a.id 
                     WHERE p.status IN ('disetujui', 'menunggu_konfirmasi')");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catat pengembalian | Peminjaman Alat</title>
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
    <div class="page-head">
        <h1>Catat pengembalian</h1>
    </div>

    <form action="proses_tambah.php" method="POST">
        <label for="peminjaman_id">Peminjaman</label>
        <select id="peminjaman_id" name="peminjaman_id" required>
            <option value="">Pilih peminjaman</option>
            <?php while($p = $pjm->fetch_assoc()): ?>
                <option value="<?= $p['id'] ?>"><?= $p['username'] ?> - <?= $p['alat'] ?> (jatuh tempo <?= date('d/m/Y', strtotime($p['tgl_jatuh_tempo'])) ?>)</option>
            <?php endwhile; ?>
        </select>

        <label for="tgl_kembali">Tanggal kembali</label>
        <input type="date" id="tgl_kembali" name="tgl_kembali" value="<?= date('Y-m-d') ?>" required>

        <label for="denda">Denda (Rp)</label>
        <input type="number" id="denda" name="denda" value="0" min="0">

        <div class="actions">
            <button class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
