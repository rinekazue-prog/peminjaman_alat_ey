<?php
// update tgl kembali & denda
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// data dari form
$id    = (int) ($_POST['id'] ?? 0);
$tgl   = $_POST['tgl_kembali'] ?? '';
$denda = (int) ($_POST['denda'] ?? 0);

// update
$stmt = $conn->prepare("UPDATE pengembalian SET tgl_kembali = ?, denda = ? WHERE id = ?");
$stmt->bind_param('sii', $tgl, $denda, $id);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>
