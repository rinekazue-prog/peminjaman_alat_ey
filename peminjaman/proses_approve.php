<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id   = (int) ($_GET['id'] ?? 0);
$aksi = $_GET['aksi'] ?? '';

if($aksi == 'setujui') {
    // Ambil alat_id
    $stmt = $conn->prepare("SELECT alat_id FROM peminjaman WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $pjm = $stmt->get_result()->fetch_assoc();

    if($pjm) {
        // Update status
        $stmt = $conn->prepare("UPDATE peminjaman SET status = 'disetujui' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // Kurangi stok
        $stmt = $conn->prepare("UPDATE alat SET stok = stok - 1 WHERE id = ?");
        $stmt->bind_param('i', $pjm['alat_id']);
        $stmt->execute();
    }

} else if($aksi == 'tolak') {
    $stmt = $conn->prepare("UPDATE peminjaman SET status = 'ditolak' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('location: approve.php');
exit;
?>
