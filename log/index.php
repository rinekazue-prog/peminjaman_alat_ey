<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log aktivitas | Peminjaman Alat</title>
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
        <h1>Log aktivitas</h1>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>User</th>
                <th>Aktivitas</th>
                <th>Waktu</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $data = $conn->query("SELECT log.*, users.username 
                                  FROM log_aktifitas log 
                                  LEFT JOIN users ON log.user_id = users.id 
                                  ORDER BY log.waktu DESC");
            while($r = $data->fetch_assoc()):
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $r['username'] ?? '-' ?></td>
                <td><?= $r['aksi'] ?></td>
                <td><?= date('d/m/Y H:i', strtotime($r['waktu'])) ?></td>
            </tr>
            <?php endwhile; ?>
            <?php if($no == 1): ?>
            <tr><td colspan="4" class="kosong">Belum ada aktivitas.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
