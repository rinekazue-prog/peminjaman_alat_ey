<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id    = (int) ($_POST['id'] ?? 0);
$tgl   = $_POST['tgl_kembali'] ?? '';
$denda = (int) ($_POST['denda'] ?? 0);

$stmt = $conn->prepare("UPDATE pengembalian SET tgl_kembali = ?, denda = ? WHERE id = ?");
$stmt->bind_param('sii', $tgl, $denda, $id);
$stmt->execute();

header('location: index.php');
exit;
?>
