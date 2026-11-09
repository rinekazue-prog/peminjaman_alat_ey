<?php
// daftar peminjaman. admin & petugas lihat semua, peminjam cuma punya sendiri
// alur status: pending -> disetujui -> menunggu_konfirmasi -> selesai (atau ditolak)
session_start();
// wajib login
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
// role & id user yang login
$role = $_SESSION['role'];
$user_id = $_SESSION['id'];

// label status (di db namanya pakai underscore)
$label_status = [
    'pending'              => 'Pending',
    'disetujui'            => 'Disetujui',
    'menunggu_konfirmasi'  => 'Menunggu konfirmasi',
    'selesai'              => 'Selesai',
    'ditolak'              => 'Ditolak',
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data peminjaman | Peminjaman Alat</title>
    <link rel="stylesheet" href="../assets/css/tampil.css">
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
    <a href="../index.php" class="back">Kembali</a>

    <div class="page-head">
        <h1>Data peminjaman</h1>

        <!-- tombol beda tiap role -->
        <?php if($role == 'admin'): ?>
            <a href="tambah.php" class="btn btn-primary">Tambah peminjaman</a>
        <?php elseif($role == 'petugas'): ?>
            <a href="approve.php" class="btn btn-primary">Setujui peminjaman</a>
        <?php else: ?>
            <a href="tambah.php" class="btn btn-primary">Ajukan peminjaman</a>
        <?php endif; ?>
    </div>

        <!-- flash error: tampil sekali, langsung di-unset -->
        <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Peminjam</th>
                <th>Alat</th>
                <th>Tgl pinjam</th>
                <th>Jatuh tempo</th>
                <th>Status</th>
                <th class="num">Denda</th>
                <?php if($role == 'admin'): ?>
                    <th class="aksi">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;

            // data yang tampil tergantung role
            if($role == 'admin' || $role == 'petugas') {
                // admin & petugas: semua data
                $data = $conn->query("SELECT p.*, u.username, a.nama AS alat 
                                      FROM peminjaman p 
                                      JOIN users u ON p.peminjam_id = u.id 
                                      JOIN alat a ON p.alat_id = a.id 
                                      ORDER BY p.id DESC");
            } else {
                // peminjam: difilter pakai id sendiri
                $stmt = $conn->prepare("SELECT p.*, u.username, a.nama AS alat 
                                        FROM peminjaman p 
                                        JOIN users u ON p.peminjam_id = u.id 
                                        JOIN alat a ON p.alat_id = a.id 
                                        WHERE p.peminjam_id = ?
                                        ORDER BY p.id DESC");
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $data = $stmt->get_result();
            }

            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['username'] ?></td>
                <td><?= $r['alat'] ?></td>
                <!-- tanggal tampil dd/mm/yyyy -->
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <!-- warna badge ikut nama status (lihat tampil.css) -->
                <td><span class="badge <?= $r['status'] ?>"><?= $label_status[$r['status']] ?? ucfirst($r['status']) ?></span></td>
                <!-- format rupiah, strip kalau tidak ada denda -->
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
                <?php if($role == 'admin'): ?>
                    <!-- confirm() supaya tidak kepencet hapus -->
                    <td class="aksi">
                        <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                        <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus data peminjaman ini?')">Hapus</a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
            <!-- belum ada data -->
            <?php if($no == 1): ?>
            <tr><td colspan="<?= $role == 'admin' ? 8 : 7 ?>" class="kosong">Belum ada peminjaman.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
