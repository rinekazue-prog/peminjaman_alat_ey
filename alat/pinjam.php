<?php
// halaman pinjam: form tanggal untuk satu alat (dibuka dari tombol Pinjam di Daftar alat)
session_start();
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
// hanya peminjam yang meminjam lewat halaman ini
if($_SESSION['role'] != 'peminjam') {
    header('location: index.php');
    exit;
}
include '../config/connection.php';

$alat_id = (int) ($_GET['alat_id'] ?? 0);
$stmt = $conn->prepare("SELECT alat.*, kategori.nama AS kat FROM alat LEFT JOIN kategori ON alat.kategori_id = kategori.id WHERE alat.id = ?");
$stmt->bind_param('i', $alat_id);
$stmt->execute();
$alat = $stmt->get_result()->fetch_assoc();

if(!$alat) {
    $_SESSION['error'] = 'Alat tidak ditemukan.';
    header('location: index.php');
    exit;
}
if($alat['stok'] < 1) {
    $_SESSION['error'] = "Stok '{$alat['nama']}' habis.";
    header('location: index.php');
    exit;
}

$hari_ini = date('Y-m-d');
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam alat | Peminjaman Alat</title>
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
        <h1>Pinjam alat</h1>
    </div>

    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <dl class="detail">
        <div><dt>Alat</dt><dd><?= e($alat['nama']) ?></dd></div>
        <div><dt>Kategori</dt><dd><?= e($alat['kat']) ?></dd></div>
        <div><dt>Stok</dt><dd><?= (int) $alat['stok'] ?></dd></div>
    </dl>

    <form action="../peminjaman/proses_tambah.php" method="POST">
        <input type="hidden" name="csrf" value="<?= e($csrf) ?>">
        <input type="hidden" name="alat_id" value="<?= (int) $alat['id'] ?>">

        <label for="tgl_pinjam">Tanggal pinjam</label>
        <input type="date" id="tgl_pinjam" name="tgl_pinjam" value="<?= $hari_ini ?>" min="<?= $hari_ini ?>" required>

        <label for="tgl_jatuh_tempo">Jatuh tempo (maksimal 7 hari)</label>
        <input type="date" id="tgl_jatuh_tempo" name="tgl_jatuh_tempo" required>

        <div class="actions">
            <button type="submit" class="btn btn-primary">Ajukan peminjaman</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>

<script>
// batas lama pinjam (harus sama dengan $MAX_HARI di peminjaman/proses_tambah.php)
var MAX_HARI = 7;
function aturBatas() {
    var pinjam = document.getElementById('tgl_pinjam').value;
    var tempo = document.getElementById('tgl_jatuh_tempo');
    if (!pinjam) return;
    var min = new Date(pinjam);
    min.setUTCDate(min.getUTCDate() + 1);
    var max = new Date(pinjam);
    max.setUTCDate(max.getUTCDate() + MAX_HARI);
    tempo.min = min.toISOString().slice(0, 10);
    tempo.max = max.toISOString().slice(0, 10);
    if (tempo.value && (tempo.value < tempo.min || tempo.value > tempo.max)) tempo.value = '';
}
document.getElementById('tgl_pinjam').addEventListener('change', aturBatas);
aturBatas();
</script>
</body>
</html>
