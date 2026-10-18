<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php'); exit;
}
include '../config/connection.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data kategori | Peminjaman Alat</title>
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
    <a href="../index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Data kategori</h1>
        <a href="tambah.php" class="btn btn-primary">Tambah kategori</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Deskripsi</th>
                <th class="aksi">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $data = $conn->query("SELECT * FROM kategori ORDER BY id DESC");
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['nama'] ?></td>
                <td><?= $r['deskripsi'] ?></td>
                <td class="aksi">
                    <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                    <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus kategori ini?')">Hapus</a>
                </td>
            </tr>
            <?php endwhile; ?>
            <?php if($no == 1): ?>
            <tr><td colspan="4" class="kosong">Belum ada kategori.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
