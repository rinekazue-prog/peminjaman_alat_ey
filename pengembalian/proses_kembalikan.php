<?php
// peminjam mengajukan pengembalian: status jadi 'menunggu_konfirmasi', tanggal ajukan dicatat
// alat belum dianggap kembali sampai petugas menerimanya (lihat proses_terima.php)
session_start();
// hanya peminjam, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// id dari URL, di-cast int biar aman
$id = (int) ($_GET['id'] ?? 0);
// id user yang login
$user_id = (int) $_SESSION['id'];
// hari ini = tanggal peminjam mengajukan pengembalian
$tgl_ajukan = date('Y-m-d');

// pastikan peminjaman ini milik dia dan masih 'disetujui' (cegah ajukan punya orang lain / dobel)
$stmt = $conn->prepare("SELECT * FROM peminjaman WHERE id = ? AND peminjam_id = ? AND status = 'disetujui'");
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

// data tidak valid atau sudah diproses, stop
if(!$pjm) {
    die("Data peminjaman tidak ditemukan atau sudah diproses!");
}

// tandai pengajuan pengembalian. alat BELUM dianggap kembali
// sampai petugas konfirmasi di pengembalian/terima.php
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'menunggu_konfirmasi', tgl_ajukan_kembali = ? WHERE id = ?");
$stmt->bind_param('si', $tgl_ajukan, $id);
$stmt->execute();

// perubahan status ini otomatis masuk log_aktifitas lewat trigger tr_log_update

// balik ke halaman kembalikan + notif sukses
header('location: kembalikan.php?status=success');
exit;
?>
