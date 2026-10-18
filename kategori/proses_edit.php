<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

$id   = (int) ($_POST['id'] ?? 0);
$nama = trim($_POST['nama'] ?? '');
$desk = trim($_POST['deskripsi'] ?? '');

$stmt = $conn->prepare("UPDATE kategori SET nama = ?, deskripsi = ? WHERE id = ?");
$stmt->bind_param('ssi', $nama, $desk, $id);
$stmt->execute();

header('location: index.php');
exit;
?>
