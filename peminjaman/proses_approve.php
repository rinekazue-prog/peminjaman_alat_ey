<?php
// setujui: status jadi 'disetujui' dan stok alat berkurang 1
// tolak: cuma ganti status, stok tidak berubah
session_start();
// hanya petugas, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// id peminjaman + aksi (setujui / tolak) dari URL
$id   = (int) ($_GET['id'] ?? 0);
$aksi = $_GET['aksi'] ?? '';

if($aksi == 'setujui') {
    // setujui: status -> disetujui, stok alat -1
    // butuh alat_id buat tahu stok mana yang dikurangi
    $stmt = $conn->prepare("SELECT alat_id FROM peminjaman WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $pjm = $stmt->get_result()->fetch_assoc();

    if($pjm) {
        // ubah status
        $stmt = $conn->prepare("UPDATE peminjaman SET status = 'disetujui' WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // stok -1 (alat sedang dipinjam)
        $stmt = $conn->prepare("UPDATE alat SET stok = stok - 1 WHERE id = ?");
        $stmt->bind_param('i', $pjm['alat_id']);
        $stmt->execute();
    }

} else if($aksi == 'tolak') {
    // tolak: ganti status saja, stok tidak disentuh
    $stmt = $conn->prepare("UPDATE peminjaman SET status = 'ditolak' WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

// balik ke daftar pengajuan
header('location: approve.php');
exit;
?>
