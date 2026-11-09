<?php
// simpan kategori baru
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

// data dari form
$nama = trim($_POST['nama'] ?? '');
$desk = trim($_POST['deskripsi'] ?? '');

// simpan ke db
$stmt = $conn->prepare("INSERT INTO kategori (nama, deskripsi) VALUES (?, ?)");
$stmt->bind_param('ss', $nama, $desk);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>
