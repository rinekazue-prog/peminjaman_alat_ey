<?php
// form pengajuan pinjam. peminjam otomatis atas nama sendiri, admin bisa pilih peminjam
// petugas tidak boleh mengajukan
session_start();
// wajib login
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
// petugas tidak ikut meminjam, lempar balik ke daftar
if($_SESSION['role'] == 'petugas') {
    header('location: index.php');
    exit;
}
// peminjam mengajukan lewat tombol Pinjam di Daftar alat
if($_SESSION['role'] == 'peminjam') {
    header('location: ../alat/index.php');
    exit;
}
include '../config/connection.php';
// role & id user yang login
$role = $_SESSION['role'];
$user_id = $_SESSION['id'];

// daftar peminjam, dipakai dropdown admin
$users = $conn->query("SELECT * FROM users WHERE role='peminjam'");
// cuma alat yang stoknya masih ada
$alats = $conn->query("SELECT * FROM alat WHERE stok > 0");
// hari ini = batas minimal tanggal pinjam
$hari_ini = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $role == 'admin' ? 'Tambah peminjaman' : 'Ajukan peminjaman' ?> | Peminjaman Alat</title>
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
    <div class="page-head">
        <h1><?= $role == 'admin' ? 'Tambah peminjaman' : 'Ajukan peminjaman' ?></h1>
    </div>

    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <!-- submit ke proses_tambah.php -->
    <form action="proses_tambah.php" method="POST">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') ?>">

        <?php if($role == 'admin'): ?>
            <!-- admin boleh pilih peminjam -->
            <label for="peminjam_id">Peminjam</label>
            <select id="peminjam_id" name="peminjam_id" required>
                <!-- opsi dari db -->
                <?php while($u = $users->fetch_assoc()): ?>
                    <option value="<?= $u['id'] ?>"><?= $u['username'] ?></option>
                <?php endwhile; ?>
            </select>
        <?php else: ?>
            <!-- peminjam: nama sendiri, tidak bisa diganti -->
            <input type="hidden" name="peminjam_id" value="<?= $user_id ?>">
            <label for="peminjam">Peminjam</label>
            <input type="text" id="peminjam" value="<?= $_SESSION['username'] ?>" readonly>
        <?php endif; ?>

        <label for="alat_id">Alat</label>
        <select id="alat_id" name="alat_id" required>
            <!-- opsi dari db -->
            <?php while($a = $alats->fetch_assoc()): ?>
                <option value="<?= $a['id'] ?>"><?= $a['nama'] ?> (stok <?= $a['stok'] ?>)</option>
            <?php endwhile; ?>
        </select>

        <label for="tgl_pinjam">Tanggal pinjam</label>
        <!-- min = hari ini (dicek lagi di server) -->
        <input type="date" id="tgl_pinjam" name="tgl_pinjam" value="<?= $hari_ini ?>" min="<?= $hari_ini ?>" required>

        <label for="tgl_jatuh_tempo">Tanggal jatuh tempo</label>
        <!-- min ikut tanggal pinjam, diatur script di bawah -->
        <input type="date" id="tgl_jatuh_tempo" name="tgl_jatuh_tempo" min="<?= $hari_ini ?>" required>

        <div class="actions">
            <button class="btn btn-primary"><?= $role == 'admin' ? 'Simpan' : 'Ajukan' ?></button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>

<script>
// batas lama pinjam (harus sama dengan $MAX_HARI di proses_tambah.php)
var MAX_HARI = 7;

// jatuh tempo: minimal tanggal pinjam, maksimal tanggal pinjam + MAX_HARI
function aturBatas() {
    var pinjam = document.getElementById('tgl_pinjam').value;
    if (!pinjam) return;
    var tempo = document.getElementById('tgl_jatuh_tempo');
    tempo.min = pinjam;
    var d = new Date(pinjam);
    d.setUTCDate(d.getUTCDate() + MAX_HARI);
    tempo.max = d.toISOString().slice(0, 10);
}
document.getElementById('tgl_pinjam').addEventListener('change', aturBatas);
aturBatas();
</script>

</body>
</html>