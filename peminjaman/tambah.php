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
    <div class="page-head">
        <h1><?= $role == 'admin' ? 'Tambah peminjaman' : 'Ajukan peminjaman' ?></h1>
    </div>

    <!-- submit ke proses_tambah.php -->
    <form action="proses_tambah.php" method="POST">

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
// jatuh tempo tidak boleh sebelum tanggal pinjam
document.getElementById('tgl_pinjam').addEventListener('change', function() {
    document.getElementById('tgl_jatuh_tempo').min = this.value;
});
</script>

</body>
</html>