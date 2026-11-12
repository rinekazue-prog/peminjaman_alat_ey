<?php
// halaman kembalikan: konfirmasi pengajuan pengembalian untuk satu peminjaman yang sedang berjalan
session_start();
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
if($_SESSION['role'] != 'peminjam') {
    header('location: index.php');
    exit;
}
include '../config/connection.php';

$id = (int) ($_GET['id'] ?? 0);
$user_id = (int) $_SESSION['id'];

// harus milik user ini dan masih 'disetujui'
$stmt = $conn->prepare("SELECT p.*, a.nama AS alat FROM peminjaman p JOIN alat a ON p.alat_id = a.id
                        WHERE p.id = ? AND p.peminjam_id = ? AND p.status = 'disetujui'");
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

if(!$pjm) {
    $_SESSION['error'] = 'Data peminjaman tidak ditemukan atau sudah diproses.';
    header('location: index.php');
    exit;
}

// perkiraan denda kalau dikembalikan hari ini (Rp 5.000 per hari telat)
$hari_ini = date('Y-m-d');
$denda = 0;
if($hari_ini > $pjm['tgl_jatuh_tempo']) {
    $denda = (int) round((strtotime($hari_ini) - strtotime($pjm['tgl_jatuh_tempo'])) / 86400 * 5000);
}
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kembalikan alat | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css?v=2">
</head>
<body>
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
        <h1>Kembalikan alat</h1>
    </div>

    <dl class="detail">
        <div><dt>Alat</dt><dd><?= e($pjm['alat']) ?></dd></div>
        <div><dt>Tgl pinjam</dt><dd><?= date('d/m/Y', strtotime($pjm['tgl_pinjam'])) ?></dd></div>
        <div><dt>Jatuh tempo</dt><dd><?= date('d/m/Y', strtotime($pjm['tgl_jatuh_tempo'])) ?></dd></div>
        <div><dt>Denda saat ini</dt><dd><?= $denda > 0 ? 'Rp ' . number_format($denda, 0, ',', '.') : '-' ?></dd></div>
    </dl>

    <form action="../pengembalian/proses_kembalikan.php" method="POST">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="id" value="<?= (int) $pjm['id'] ?>">
        <p>Pastikan alat sudah kamu serahkan ke petugas. Pengembalian dianggap selesai setelah petugas mengonfirmasi.</p>
        <div class="actions">
            <button type="submit" class="btn btn-primary">Ajukan pengembalian</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
