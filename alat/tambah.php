<?php
// form tambah alat (admin). kategori diambil dari tabel kategori buat dropdown
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';
// kategori buat isi dropdown
$kats = $conn->query("SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah alat | Peminjaman Alat</title>
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
        <h1>Tambah alat</h1>
    </div>

    <!-- submit ke proses_tambah.php -->
    <form action="proses_tambah.php" method="POST">
        <label for="nama">Nama alat</label>
        <input type="text" id="nama" name="nama" required>

        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" required>
            <option value="">Pilih kategori</option>
            <!-- opsi dari db -->
            <?php while($k = $kats->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>"><?= $k['nama'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" min="0" required>

        <div class="actions">
            <button class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
