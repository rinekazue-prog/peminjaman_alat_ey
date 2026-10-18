<?php
session_start();

if(!isset($_SESSION['role'])) {
    header('location: login.php');
    exit;
}

$role = $_SESSION['role'];

$nama_role = [
    'admin'    => 'Administrator',
    'petugas'  => 'Petugas',
    'peminjam' => 'Peminjam',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda | Peminjaman Alat</title>
    <link rel="stylesheet" href="assets/css/tampil.css">
</head>
<body>
<header class="topbar">
    <a href="index.php" class="brand">Peminjaman Alat</a>
    <div class="user">
        <span><?= $_SESSION['username'] ?></span>
        <a href="logout.php">Keluar</a>
    </div>
</header>

<main class="page">
    <h1 class="greeting">Aku, <?= $_SESSION['username'] ?></h1>
    <p class="sub">Masuk sebagai <?= $nama_role[$role] ?? 'Peminjam' ?></p>

    <div class="menu-grid">
    <?php if($role == 'admin'): ?>

        <section class="menu">
            <h2>Data master</h2>
            <ul>
                <li><a href="user/index.php">Data user</a></li>
                <li><a href="kategori/index.php">Data kategori</a></li>
                <li><a href="alat/index.php">Data alat</a></li>
            </ul>
        </section>

        <section class="menu">
            <h2>Transaksi</h2>
            <ul>
                <li><a href="peminjaman/index.php">Data peminjaman</a></li>
                <li><a href="pengembalian/index.php">Data pengembalian</a></li>
            </ul>
        </section>

        <section class="menu">
            <h2>Pantauan</h2>
            <ul>
                <li><a href="log/index.php">Log aktivitas</a></li>
            </ul>
        </section>

    <?php elseif($role == 'petugas'): ?>

        <section class="menu">
            <h2>Transaksi</h2>
            <ul>
                <li><a href="peminjaman/approve.php">Setujui peminjaman</a></li>
                <li><a href="peminjaman/index.php">Data peminjaman</a></li>
                <li><a href="pengembalian/terima.php">Terima pengembalian</a></li>
                <li><a href="pengembalian/pantau.php">Pantau pengembalian</a></li>
            </ul>
        </section>

        <section class="menu">
            <h2>Laporan</h2>
            <ul>
                <li><a href="laporan/index.php">Laporan peminjaman</a></li>
            </ul>
        </section>

    <?php else: ?>

        <section class="menu">
            <h2>Alat</h2>
            <ul>
                <li><a href="alat/index.php">Daftar alat</a></li>
            </ul>
        </section>

        <section class="menu">
            <h2>Peminjaman</h2>
            <ul>
                <li><a href="peminjaman/tambah.php">Ajukan peminjaman</a></li>
                <li><a href="pengembalian/kembalikan.php">Kembalikan alat</a></li>
                <li><a href="riwayat.php">Riwayat peminjaman</a></li>
            </ul>
        </section>

    <?php endif; ?>
    </div>
</main>
</body>
</html>
