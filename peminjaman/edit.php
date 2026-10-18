<?php
session_start();
// HANYA ADMIN yang boleh edit
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM peminjaman WHERE id = ?");
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
    <title>Edit peminjaman | Peminjaman Alat</title>
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
        <h1>Edit peminjaman</h1>
    </div>

    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">

        <label for="status">Status</label>
        <select id="status" name="status" required>
            <option value="pending" <?= $r['status']=='pending'?'selected':'' ?>>Pending</option>
            <option value="disetujui" <?= $r['status']=='disetujui'?'selected':'' ?>>Disetujui</option>
            <option value="menunggu_konfirmasi" <?= $r['status']=='menunggu_konfirmasi'?'selected':'' ?>>Menunggu konfirmasi petugas</option>
            <option value="ditolak" <?= $r['status']=='ditolak'?'selected':'' ?>>Ditolak</option>
            <option value="selesai" <?= $r['status']=='selesai'?'selected':'' ?>>Selesai</option>
        </select>

        <label for="denda">Denda (Rp)</label>
        <input type="number" id="denda" name="denda" value="<?= $r['denda'] ?>" min="0">

        <div class="actions">
            <button class="btn btn-primary">Simpan perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
