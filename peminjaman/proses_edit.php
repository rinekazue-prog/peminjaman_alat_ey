<?php
// update status & denda hasil koreksi admin
session_start();
// hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// data dari form
$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$denda  = (int) ($_POST['denda'] ?? 0);

// status harus salah satu dari 5 nilai ini, selain itu abaikan
if(!in_array($status, ['pending', 'disetujui', 'menunggu_konfirmasi', 'ditolak', 'selesai'])) {
    header('location: index.php');
    exit;
}

// update
$stmt = $conn->prepare("UPDATE peminjaman SET status = ?, denda = ? WHERE id = ?");
$stmt->bind_param('sii', $status, $denda, $id);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>
