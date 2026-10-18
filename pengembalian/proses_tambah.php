<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$pid   = (int) ($_POST['peminjaman_id'] ?? 0);
$tgl   = $_POST['tgl_kembali'] ?? '';
$denda = (int) ($_POST['denda'] ?? 0);

// Insert pengembalian
$stmt = $conn->prepare("INSERT INTO pengembalian (peminjaman_id, tgl_kembali, denda) VALUES (?, ?, ?)");
$stmt->bind_param('isi', $pid, $tgl, $denda);
$stmt->execute();

// Update status peminjaman jadi selesai
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'selesai' WHERE id = ?");
$stmt->bind_param('i', $pid);
$stmt->execute();

// Ambil alat_id untuk tambah stok kembali
$stmt = $conn->prepare("SELECT alat_id FROM peminjaman WHERE id = ?");
$stmt->bind_param('i', $pid);
$stmt->execute();
$alat = $stmt->get_result()->fetch_assoc();

if($alat) {
    $stmt = $conn->prepare("UPDATE alat SET stok = stok + 1 WHERE id = ?");
    $stmt->bind_param('i', $alat['alat_id']);
    $stmt->execute();
}

header('location: index.php');
exit;
?>
