<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';
$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM kategori WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if(!$r) {
    header('location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit kategori | Peminjaman Alat</title>
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
        <h1>Edit kategori</h1>
    </div>

    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">

        <label for="nama">Nama kategori</label>
        <input type="text" id="nama" name="nama" value="<?= $r['nama'] ?>" required>

        <label for="deskripsi">Deskripsi</label>
        <input type="text" id="deskripsi" name="deskripsi" value="<?= $r['deskripsi'] ?>" required>

        <div class="actions">
            <button class="btn btn-primary">Simpan perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
