<?php
// daftar alat + status Tersedia/Habis (dilihat dari stok), bisa dicari
// admin: tombol tambah/edit/hapus
// peminjam: tombol Pinjam -> halaman pinjam.php, tombol Kembalikan (kalau sedang meminjam) -> halaman kembali.php
session_start();
// wajib login
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
// role dipakai buat nampilin/menyembunyikan tombol
$role = $_SESSION['role'];
$user_id = (int) $_SESSION['id'];
// admin dan peminjam punya kolom Aksi, petugas tidak
$ada_aksi = in_array($role, ['admin', 'peminjam']);

// label status pengajuan yang masih berjalan (status 'disetujui' punya tombol Kembalikan)
$label_proses = [
    'pending'             => 'Menunggu persetujuan',
    'menunggu_konfirmasi' => 'Menunggu konfirmasi petugas',
];

// ---------- pencarian (?q=...) cari di nama alat atau kategori ----------
$q = trim($_GET['q'] ?? '');

// pinjam_id     = peminjaman user ini yang 'disetujui' untuk alat tsb (kalau ada, tombol Kembalikan muncul)
// status_proses = status pengajuan user ini yang masih berjalan (pending / menunggu_konfirmasi)
$sql = "SELECT alat.*, kategori.nama AS kat,
               (SELECT p.id FROM peminjaman p
                WHERE p.alat_id = alat.id AND p.peminjam_id = ? AND p.status = 'disetujui'
                ORDER BY p.id DESC LIMIT 1) AS pinjam_id,
               (SELECT p.status FROM peminjaman p
                WHERE p.alat_id = alat.id AND p.peminjam_id = ? AND p.status IN ('pending', 'menunggu_konfirmasi')
                ORDER BY p.id DESC LIMIT 1) AS status_proses
        FROM alat
        LEFT JOIN kategori ON alat.kategori_id = kategori.id";
$tipe = 'ii';
$params = [$user_id, $user_id];
if($q !== '') {
    $sql .= " WHERE alat.nama LIKE ? OR kategori.nama LIKE ?";
    $like = '%' . $q . '%';
    $tipe .= 'ss';
    $params[] = $like;
    $params[] = $like;
}
$sql .= " ORDER BY alat.id DESC";
[$data, $page, $pages, $total, $offset] = paginate($conn, $sql, $tipe, $params);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar alat | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css?v=2">
</head>
<body>
<!-- topbar -->
<header class="topbar">
    <a href="../index.php" class="brand">Peminjaman Alat</a>
    <div class="user">
        <span><?= e($_SESSION['username']) ?></span>
        <a href="../logout.php">Keluar</a>
    </div>
</header>

<main class="page">
    <a href="../index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Daftar alat</h1>
        <?php if($role == 'admin'): ?>
            <!-- cuma admin yang boleh tambah -->
            <a href="tambah.php" class="btn btn-primary">Tambah alat</a>
        <?php endif; ?>
    </div>

    <!-- flash sukses: tampil sekali, langsung di-unset -->
    <?php if(isset($_SESSION['success'])): ?>
        <p class="notice notice-ok" role="status"><?= e($_SESSION['success']); unset($_SESSION['success']); ?></p>
    <?php endif; ?>

    <!-- flash error: tampil sekali, langsung di-unset -->
    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= e($_SESSION['error']); unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <!-- form kosong tersembunyi: input & tombol tersambung lewat atribut form="cari" -->
    <form method="get" id="cari" hidden></form>
    <div class="toolbar">
        <input type="text" name="q" form="cari" value="<?= e($q) ?>" placeholder="Cari nama alat atau kategori..." aria-label="Cari">
        <button type="submit" form="cari" class="btn btn-primary">Cari</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama alat</th>
                <th>Kategori</th>
                <th class="num">Stok</th>
                <th>Status</th>
                <?php if($ada_aksi): ?>
                    <th class="aksi">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = $offset + 1;
            // $data sudah disiapkan di atas (alat + kategori + filter)
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= e($r['nama']) ?></td>
                <td><?= e($r['kat']) ?></td>
                <td class="num"><?= $r['stok'] ?></td>
                <!-- stok > 0 = Tersedia, selain itu Habis -->
                <td><span class="badge <?= $r['stok'] > 0 ? 'tersedia' : 'habis' ?>"><?= $r['stok'] > 0 ? 'Tersedia' : 'Habis' ?></span></td>

                <?php if($role == 'admin'): ?>
                    <!-- confirm() supaya tidak kepencet hapus -->
                    <td class="aksi">
                        <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                        <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus alat ini?')">Hapus</a>
                    </td>

                <?php elseif($role == 'peminjam'): ?>
                    <td class="aksi">
                        <?php if($r['stok'] > 0): ?>
                            <!-- ke halaman pinjam -->
                            <a href="pinjam.php?alat_id=<?= (int) $r['id'] ?>" class="btn btn-primary btn-sm">Pinjam</a>
                        <?php endif; ?>
                        <?php if($r['pinjam_id']): ?>
                            <!-- alat ini sedang dipinjam user ini: ke halaman kembali -->
                            <a href="kembali.php?id=<?= (int) $r['pinjam_id'] ?>" class="btn btn-secondary btn-sm">Kembalikan</a>
                        <?php endif; ?>
                        <?php if($r['status_proses']): ?>
                            <span class="info-status"><?= e($label_proses[$r['status_proses']] ?? '') ?></span>
                        <?php endif; ?>
                        <?php if($r['stok'] <= 0 && !$r['pinjam_id'] && !$r['status_proses']): ?>-<?php endif; ?>
                    </td>
                <?php endif; ?>
            </tr>

            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($total == 0): ?>
            <tr><td colspan="<?= $ada_aksi ? 6 : 5 ?>" class="kosong"><?= $q !== '' ? 'Alat yang dicari tidak ditemukan.' : 'Belum ada alat.' ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <?php tampil_halaman($page, $pages); ?>
</main>

</body>
</html>
