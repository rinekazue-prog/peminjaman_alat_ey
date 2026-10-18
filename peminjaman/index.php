<?php
session_start();
if(!isset($_SESSION['role'])) {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
$role = $_SESSION['role'];
$user_id = $_SESSION['id'];

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

        <!-- Tombol beda sesuai role -->
        <?php if($role == 'admin'): ?>
            <a href="tambah.php" class="btn btn-primary">Tambah peminjaman</a>
        <?php elseif($role == 'petugas'): ?>
            <a href="approve.php" class="btn btn-primary">Setujui peminjaman</a>
        <?php else: ?>
            <a href="tambah.php" class="btn btn-primary">Ajukan peminjaman</a>
        <?php endif; ?>
    </div>

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

            // Query beda sesuai role
            if($role == 'admin' || $role == 'petugas') {
                // Admin & Petugas: lihat semua
                $data = $conn->query("SELECT p.*, u.username, a.nama AS alat 
                                      FROM peminjaman p 
                                      JOIN users u ON p.peminjam_id = u.id 
                                      JOIN alat a ON p.alat_id = a.id 
                                      ORDER BY p.id DESC");
            } else {
                // Peminjam: cuma lihat punya mereka sendiri
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
                <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                <td><span class="badge <?= $r['status'] ?>"><?= $label_status[$r['status']] ?? ucfirst($r['status']) ?></span></td>
                <td class="num"><?= $r['denda'] > 0 ? 'Rp ' . number_format($r['denda'], 0, ',', '.') : '-' ?></td>
                <?php if($role == 'admin'): ?>
                    <td class="aksi">
                        <a href="edit.php?id=<?= $r['id'] ?>">Edit</a>
                        <a href="hapus.php?id=<?= $r['id'] ?>" class="hapus" onclick="return confirm('Hapus data peminjaman ini?')">Hapus</a>
                    </td>
                <?php endif; ?>
            </tr>
            <?php endwhile; ?>
            <?php if($no == 1): ?>
            <tr><td colspan="<?= $role == 'admin' ? 8 : 7 ?>" class="kosong">Belum ada peminjaman.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
