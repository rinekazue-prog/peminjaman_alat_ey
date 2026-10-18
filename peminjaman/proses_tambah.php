<?php
session_start();

// Admin dan peminjam boleh mengajukan, petugas tidak
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'peminjam'])) {
    header('location: ../login.php');
    exit;
}

include '../config/connection.php';

// Peminjam hanya boleh mengajukan atas namanya sendiri
if($_SESSION['role'] == 'peminjam') {
    $pid = (int) $_SESSION['id'];
} else {
    $pid = (int) ($_POST['peminjam_id'] ?? 0);
}

$aid   = (int) ($_POST['alat_id'] ?? 0);
$tgl   = $_POST['tgl_pinjam'] ?? '';
$tempo = $_POST['tgl_jatuh_tempo'] ?? '';
$hari_ini = date('Y-m-d');

// ==========================================
// VALIDASI TANGGAL (Sisi Server)
// ==========================================

// 1. Cek apakah tanggal pinjam di masa lalu
if ($tgl < $hari_ini) {
    echo "<script>alert('Tanggal pinjam tidak boleh di masa lalu!'); window.history.back();</script>";
    exit;
}

// 2. Cek apakah jatuh tempo sebelum atau sama dengan tanggal pinjam
if ($tempo <= $tgl) {
    echo "<script>alert('Tanggal jatuh tempo harus setelah tanggal pinjam!'); window.history.back();</script>";
    exit;
}

// ==========================================
// PROSES INSERT KE DATABASE (Prepared Statement)
// ==========================================
$stmt = $conn->prepare("INSERT INTO peminjaman (peminjam_id, alat_id, tgl_pinjam, tgl_jatuh_tempo, status, denda) VALUES (?, ?, ?, ?, 'pending', 0)");
$stmt->bind_param('iiss', $pid, $aid, $tgl, $tempo);
$stmt->execute();

header('location: index.php');
exit;
?>