<?php
// hapus satu data pengembalian
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// id yang mau dihapus
$id = (int) ($_GET['id'] ?? 0);

// hapus
$stmt = $conn->prepare("DELETE FROM pengembalian WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();

// balik ke daftar
header('location: index.php');
exit;
?>
