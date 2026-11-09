<?php
// catat pengembalian manual: insert -> status peminjaman 'selesai' -> stok alat +1
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// data dari form
$pid   = (int) ($_POST['peminjaman_id'] ?? 0);
$tgl   = $_POST['tgl_kembali'] ?? '';
$denda = (int) ($_POST['denda'] ?? 0);

// catat pengembalian
$stmt = $conn->prepare("INSERT INTO pengembalian (peminjaman_id, tgl_kembali, denda) VALUES (?, ?, ?)");
$stmt->bind_param('isi', $pid, $tgl, $denda);
$stmt->execute();

// peminjaman ditandai selesai
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'selesai' WHERE id = ?");
$stmt->bind_param('i', $pid);
$stmt->execute();

// ambil alat_id buat mengembalikan stok
$stmt = $conn->prepare("SELECT alat_id FROM peminjaman WHERE id = ?");
$stmt->bind_param('i', $pid);
$stmt->execute();
$alat = $stmt->get_result()->fetch_assoc();

if($alat) {
    // stok alat +1 (alat sudah kembali)
    $stmt = $conn->prepare("UPDATE alat SET stok = stok + 1 WHERE id = ?");
    $stmt->bind_param('i', $alat['alat_id']);
    $stmt->execute();
}

// balik ke daftar
header('location: index.php');
exit;
?>
