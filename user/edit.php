<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
$id = (int) ($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
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
    <title>Edit user | Peminjaman Alat</title>
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
        <h1>Edit user</h1>
    </div>

    <form action="proses_edit.php" method="POST">
        <input type="hidden" name="id" value="<?= $r['id'] ?>">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= $r['username'] ?>" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password">
        <span class="hint">Kosongkan jika tidak ingin mengubah password.</span>

        <label for="role">Role</label>
        <select id="role" name="role" required>
            <option value="admin" <?= $r['role']=='admin'?'selected':'' ?>>Admin</option>
            <option value="petugas" <?= $r['role']=='petugas'?'selected':'' ?>>Petugas</option>
            <option value="peminjam" <?= $r['role']=='peminjam'?'selected':'' ?>>Peminjam</option>
        </select>

        <div class="actions">
            <button class="btn btn-primary">Simpan perubahan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</main>
</body>
</html>
