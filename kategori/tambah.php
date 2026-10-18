<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah kategori | Peminjaman Alat</title>
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
        <h1>Tambah kategori</h1>
    </div>

    <form action="proses_tambah.php" method="POST">
        <label for="nama">Nama kategori</label>
        <input type="text" id="nama" name="nama" required>

        <label for="deskripsi">Deskripsi</label>
        <input type="text" id="deskripsi" name="deskripsi" required>

        <div class="actions">
            <button class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
