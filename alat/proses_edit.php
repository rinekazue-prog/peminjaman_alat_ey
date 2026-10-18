<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

$id   = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$kat  = (int) ($_POST['kategori_id'] ?? 0);
$stok = (int) ($_POST['stok'] ?? 0);

$stmt = $conn->prepare("UPDATE alat SET nama = ?, kategori_id = ?, stok = ? WHERE id = ?");
$stmt->bind_param('siii', $nama, $kat, $stok, $id);
$stmt->execute();

header('location: index.php');
exit;
?>
