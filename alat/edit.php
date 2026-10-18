<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';
$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM alat WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();

if(!$r) {
    header('location: index.php');
    exit;
}
$kats = $conn->query("SELECT * FROM kategori");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit alat | Peminjaman Alat</title>
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
        <h1>Edit alat</h1>
    </div>

    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">

        <label for="nama">Nama alat</label>
        <input type="text" id="nama" name="nama" value="<?= $r['nama'] ?>" required>

        <label for="kategori_id">Kategori</label>
        <select id="kategori_id" name="kategori_id" required>
            <?php while($k = $kats->fetch_assoc()): ?>
                <option value="<?= $k['id'] ?>" <?= $r['kategori_id']==$k['id']?'selected':'' ?>><?= $k['nama'] ?></option>
            <?php endwhile; ?>
        </select>

        <label for="stok">Stok</label>
        <input type="number" id="stok" name="stok" value="<?= $r['stok'] ?>" min="0" required>

        <div class="actions">
            <button class="btn btn-primary">Simpan perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
