<?php
// beranda: menu yang tampil beda tiap role (admin / petugas / peminjam)
// session = tempat status login disimpan (id, username, role)
session_start();

// wajib login
if(!isset($_SESSION['role'])) {
    header('location: login.php');
    exit;
}

// role menentukan menu yang tampil
$role = $_SESSION['role'];

// nama role versi tampilan
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
    <link rel="stylesheet" href="assets/css/tampil.css?v=2">
</head>
<body>
<!-- topbar -->
<header class="topbar">
    <a href="index.php" class="brand">Peminjaman Alat</a>
    <div class="user">
        <span><?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></span>
        <a href="logout.php">Keluar</a>
    </div>
</header>

<main class="page">
    <h1 class="greeting">Selamat Datang, <?= htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8') ?></h1>
    <p class="sub">Masuk sebagai <?= $nama_role[$role] ?? 'Peminjam' ?></p>

    <!-- isi menu ditentukan role -->
    <div class="menu-grid">
    <!-- admin -->
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

    <!-- petugas -->
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

    <!-- peminjam -->
    <?php else: ?>

        <section class="menu">
            <h2>Alat</h2>
            <ul>
                <li><a href="alat/index.php">Daftar alat (pinjam dan kembalikan)</a></li>
            </ul>
        </section>

        <section class="menu">
            <h2>Riwayat</h2>
            <ul>
                <li><a href="riwayat.php">Riwayat peminjaman</a></li>
            </ul>
        </section>

    <?php endif; ?>
    </div>
</main>
</body>
</html>
