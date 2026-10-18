<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

$nama = trim($_POST['nama'] ?? '');
$kat  = (int) ($_POST['kategori_id'] ?? 0);
$stok = (int) ($_POST['stok'] ?? 0);

$stmt = $conn->prepare("INSERT INTO alat (nama, kategori_id, stok) VALUES (?, ?, ?)");
$stmt->bind_param('sii', $nama, $kat, $stok);
$stmt->execute();

header('location: index.php');
exit;
?>
