<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id = (int) ($_GET['id'] ?? 0);
$user_id = (int) $_SESSION['id'];
$tgl_ajukan = date('Y-m-d');

// Ambil data peminjaman (harus milik user yang login & masih 'disetujui')
$stmt = $conn->prepare("SELECT * FROM peminjaman WHERE id = ? AND peminjam_id = ? AND status = 'disetujui'");
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

if(!$pjm) {
    die("Data peminjaman tidak ditemukan atau sudah diproses!");
}

// Tandai peminjam sudah mengajukan pengembalian, alat BELUM dianggap kembali
// sampai petugas mengonfirmasi penerimaannya di pengembalian/terima.php
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'menunggu_konfirmasi', tgl_ajukan_kembali = ? WHERE id = ?");
$stmt->bind_param('si', $tgl_ajukan, $id);
$stmt->execute();

// Perubahan status ini otomatis tercatat di log_aktifitas lewat trigger tr_log_update

header('location: kembalikan.php?status=success');
exit;
?>
