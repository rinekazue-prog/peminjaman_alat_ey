<?php
session_start();
// HANYA ADMIN yang boleh edit
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';
$denda  = (int) ($_POST['denda'] ?? 0);

// status hanya boleh salah satu dari ini
if(!in_array($status, ['pending', 'disetujui', 'ditolak', 'selesai'])) {
    header('location: index.php');
    exit;
}

$stmt = $conn->prepare("UPDATE peminjaman SET status = ?, denda = ? WHERE id = ?");
$stmt->bind_param('sii', $status, $denda, $id);
$stmt->execute();

header('location: index.php');
exit;
?>
