<?php
// update nama & deskripsi kategori
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

// data dari form
$id   = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$desk = trim($_POST['deskripsi'] ?? '');

// update
$stmt = $conn->prepare("UPDATE kategori SET nama = ?, deskripsi = ? WHERE id = ?");
$stmt->bind_param('ssi', $nama, $desk, $id);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>
