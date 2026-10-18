<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM kategori WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

header('location: index.php');
exit;
?>
