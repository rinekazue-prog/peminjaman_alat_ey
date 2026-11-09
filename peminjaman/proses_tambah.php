<?php
// simpan pengajuan dengan status pending, tanggal dicek lagi di server
session_start();

// admin & peminjam boleh mengajukan, petugas tidak
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'peminjam'])) {
    header('location: ../login.php');
    exit;
}

include '../config/connection.php';

// peminjam dipaksa pakai id sendiri, peminjam_id dari form diabaikan (bisa dimanipulasi)
if($_SESSION['role'] == 'peminjam') {
    $pid = (int) $_SESSION['id'];
} else {
    $pid = (int) ($_POST['peminjam_id'] ?? 0);
}

// data dari form
$aid   = (int) ($_POST['alat_id'] ?? 0);
$tgl   = $_POST['tgl_pinjam'] ?? '';
$tempo = $_POST['tgl_jatuh_tempo'] ?? '';
$hari_ini = date('Y-m-d');

// validasi tanggal di server, karena cek di browser gampang dilewati

// tanggal pinjam tidak boleh di masa lalu
if ($tgl < $hari_ini) {
    echo "<script>alert('Tanggal pinjam tidak boleh di masa lalu!'); window.history.back();</script>";
    exit;
}

// jatuh tempo harus setelah tanggal pinjam
if ($tempo <= $tgl) {
    echo "<script>alert('Tanggal jatuh tempo harus setelah tanggal pinjam!'); window.history.back();</script>";
    exit;
}

// simpan: status awal pending, denda 0
$stmt = $conn->prepare("INSERT INTO peminjaman (peminjam_id, alat_id, tgl_pinjam, tgl_jatuh_tempo, status, denda) VALUES (?, ?, ?, ?, 'pending', 0)");
$stmt->bind_param('iiss', $pid, $aid, $tgl, $tempo);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>