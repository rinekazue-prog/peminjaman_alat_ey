<?php
// hapus alat (admin). diblok kalau alat sudah pernah dipinjam supaya riwayat tidak rusak
session_start();
// hanya admin, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') { header('location: ../login.php'); exit; }
include '../config/connection.php';

// id yang mau dihapus
$id = (int) ($_GET['id'] ?? 0);

// alat yang sudah punya riwayat tidak boleh dihapus (terikat foreign key)
$stmt = $conn->prepare("SELECT COUNT(*) AS jml FROM peminjaman WHERE alat_id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$jml = $stmt->get_result()->fetch_assoc()['jml'];

// punya riwayat, batal hapus
if ($jml > 0) {
    $_SESSION['error'] = 'Alat tidak bisa dihapus karena sudah punya riwayat peminjaman.';
    header('location: index.php');
    exit;
}

// aman dihapus. try/catch buat jaga-jaga kalau db tetap menolak
try {
    // hapus
    $stmt = $conn->prepare("DELETE FROM alat WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
} catch (mysqli_sql_exception $e) {
    $_SESSION['error'] = 'Alat gagal dihapus.';
}

// balik ke daftar
header('location: index.php');
exit;
?>