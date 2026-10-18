<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
$user_id = (int) $_SESSION['id'];

// Tampilkan peminjaman milik user ini yang statusnya 'disetujui' (alat masih di tangan peminjam)
$stmt = $conn->prepare("SELECT p.*, a.nama AS alat 
                        FROM peminjaman p 
                        JOIN alat a ON p.alat_id = a.id 
                        WHERE p.peminjam_id = ? AND p.status = 'disetujui'
                        ORDER BY p.id DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$data = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kembalikan alat | Peminjaman Alat</title>
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
        <h1>Kembalikan alat</h1>
    </div>

    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <p class="notice notice-ok" role="status">Pengembalian sudah diajukan. Serahkan alatnya ke petugas, status akan berubah setelah petugas mengonfirmasi alat sudah diterima.</p>
    <?php endif; ?>

    <?php if($data->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Alat</th>
                    <th>Tgl pinjam</th>
                    <th>Jatuh tempo</th>
                    <th class="aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while($r = $data->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $r['alat'] ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_pinjam'])) ?></td>
                    <td><?= date('d/m/Y', strtotime($r['tgl_jatuh_tempo'])) ?></td>
                    <td class="aksi">
                        <a href="proses_kembalikan.php?id=<?= $r['id'] ?>"
                           class="btn btn-primary btn-sm"
                           onclick="return confirm('Ajukan pengembalian alat ini? Pastikan alat sudah kamu serahkan ke petugas.')">Kembalikan</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">Belum ada alat yang sedang dipinjam.</p>
    <?php endif; ?>
</main>
</body>
</html>
