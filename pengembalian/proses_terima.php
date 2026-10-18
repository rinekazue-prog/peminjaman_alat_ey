<?php
session_start();
// HANYA PETUGAS yang boleh memproses konfirmasi penerimaan pengembalian
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id = (int) ($_GET['id'] ?? 0);
$petugas_id = (int) $_SESSION['id'];
$tgl_konfirmasi = date('Y-m-d');

// Ambil data peminjaman yang statusnya 'menunggu_konfirmasi' (sudah diajukan peminjam)
$stmt = $conn->prepare("SELECT p.*, u.username, a.nama AS alat
                         FROM peminjaman p
                         JOIN users u ON p.peminjam_id = u.id
                         JOIN alat a ON p.alat_id = a.id
                         WHERE p.id = ? AND p.status = 'menunggu_konfirmasi'");
$stmt->bind_param('i', $id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

if(!$pjm) {
    die("Data pengajuan pengembalian tidak ditemukan atau sudah dikonfirmasi!");
}

// Tanggal kembali yang dipakai untuk hitung denda = tanggal peminjam MENGAJUKAN
// pengembalian (bukan tanggal petugas sempat konfirmasi), supaya peminjam
// tidak dirugikan kalau konfirmasi petugas telat.
$tgl_kembali = $pjm['tgl_ajukan_kembali'] ?: $tgl_konfirmasi;
$tgl_tempo   = $pjm['tgl_jatuh_tempo'];
$denda = 0;

if($tgl_kembali > $tgl_tempo) {
    $selisih_hari = (strtotime($tgl_kembali) - strtotime($tgl_tempo)) / (60*60*24);
    $denda = (int) round($selisih_hari * 5000);
}

// Catat penerimaan ke tabel pengembalian
$stmt = $conn->prepare("INSERT INTO pengembalian (peminjaman_id, tgl_kembali, denda) VALUES (?, ?, ?)");
$stmt->bind_param('isi', $id, $tgl_kembali, $denda);
$stmt->execute();

// Update status peminjaman jadi selesai
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'selesai', denda = ? WHERE id = ?");
$stmt->bind_param('ii', $denda, $id);
$stmt->execute();

// Tambah stok alat kembali (baru sekarang, setelah petugas benar-benar pegang alatnya)
$stmt = $conn->prepare("UPDATE alat SET stok = stok + 1 WHERE id = ?");
$stmt->bind_param('i', $pjm['alat_id']);
$stmt->execute();

// Catat di log bahwa PETUGAS yang mengonfirmasi alat sudah diterima
$pesan = "Mengonfirmasi penerimaan alat '{$pjm['alat']}' dari {$pjm['username']}" . ($denda > 0 ? ", denda: Rp " . number_format($denda, 0, ',', '.') : "");
$stmt = $conn->prepare("INSERT INTO log_aktifitas (user_id, aksi) VALUES (?, ?)");
$stmt->bind_param('is', $petugas_id, $pesan);
$stmt->execute();

header('location: terima.php?status=success');
exit;
?>
