<?php
// hapus peminjaman (admin) beserta data pengembaliannya
session_start();
// hanya admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// id yang mau dihapus
$id = (int) ($_GET['id'] ?? 0);

try {
    // transaksi: kalau satu langkah gagal, semuanya dibatalkan
    $conn->begin_transaction();

    // pengembalian (anak) dihapus dulu, baru peminjaman (induk)
    $stmt = $conn->prepare("DELETE FROM pengembalian WHERE peminjaman_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // lalu peminjamannya
    $stmt = $conn->prepare("DELETE FROM peminjaman WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // semua sukses, simpan permanen
    $conn->commit();
    $_SESSION['error'] = null;
    unset($_SESSION['error']);
} catch (mysqli_sql_exception $e) {
    // ada yang gagal, batalkan semua
    $conn->rollback();
    $_SESSION['error'] = 'Peminjaman gagal dihapus.';
}

// balik ke daftar
header('location: index.php');
exit;
?>