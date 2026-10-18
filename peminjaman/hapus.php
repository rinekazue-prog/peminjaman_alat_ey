<?php
session_start();
// HANYA ADMIN yang boleh hapus
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id = (int) ($_GET['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM peminjaman WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

header('location: index.php');
exit;
?>
