<?php
// update nama, kategori, dan stok alat
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

// data dari form
$id   = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$kat  = (int) ($_POST['kategori_id'] ?? 0);
$stok = (int) ($_POST['stok'] ?? 0);

// update
$stmt = $conn->prepare("UPDATE alat SET nama = ?, kategori_id = ?, stok = ? WHERE id = ?");
$stmt->bind_param('siii', $nama, $kat, $stok, $id);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>
