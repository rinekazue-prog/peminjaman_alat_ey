<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

$nama = trim($_POST['nama'] ?? '');
$desk = trim($_POST['deskripsi'] ?? '');

$stmt = $conn->prepare("INSERT INTO kategori (nama, deskripsi) VALUES (?, ?)");
$stmt->bind_param('ss', $nama, $desk);
$stmt->execute();

header('location: index.php');
exit;
?>
