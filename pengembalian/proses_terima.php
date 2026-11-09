<?php
// petugas konfirmasi alat sudah diterima:
// hitung denda -> catat pengembalian -> status 'selesai' -> stok +1 -> tulis log aktivitas
session_start();
// hanya petugas yang boleh konfirmasi penerimaan
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// id dari URL, di-cast int biar aman
$id = (int) ($_GET['id'] ?? 0);
// id petugas + tanggal konfirmasi (hari ini)
$petugas_id = (int) $_SESSION['id'];
$tgl_konfirmasi = date('Y-m-d');

// hanya yang 'menunggu_konfirmasi', jadi tidak bisa diproses dua kali
$stmt = $conn->prepare("SELECT p.*, u.username, a.nama AS alat
                         FROM peminjaman p
                         JOIN users u ON p.peminjam_id = u.id
                         JOIN alat a ON p.alat_id = a.id
                         WHERE p.id = ? AND p.status = 'menunggu_konfirmasi'");
$stmt->bind_param('i', $id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

// data tidak valid atau sudah diproses, stop
if(!$pjm) {
    die("Data pengajuan pengembalian tidak ditemukan atau sudah dikonfirmasi!");
}

// denda dihitung dari tanggal peminjam MENGAJUKAN pengembalian,
// bukan tanggal konfirmasi petugas, supaya peminjam
// tidak rugi kalau petugasnya telat konfirmasi
$tgl_kembali = $pjm['tgl_ajukan_kembali'] ?: $tgl_konfirmasi;
$tgl_tempo   = $pjm['tgl_jatuh_tempo'];
$denda = 0;

// telat? denda dihitung per hari keterlambatan
if($tgl_kembali > $tgl_tempo) {
    // selisih detik dibagi 86400 = jumlah hari telat
    $selisih_hari = (strtotime($tgl_kembali) - strtotime($tgl_tempo)) / (60*60*24);
    // tarif denda Rp 5.000 per hari
    $denda = (int) round($selisih_hari * 5000);
}

// catat ke tabel pengembalian
$stmt = $conn->prepare("INSERT INTO pengembalian (peminjaman_id, tgl_kembali, denda) VALUES (?, ?, ?)");
$stmt->bind_param('isi', $id, $tgl_kembali, $denda);
$stmt->execute();

// peminjaman ditandai selesai
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'selesai', denda = ? WHERE id = ?");
$stmt->bind_param('ii', $denda, $id);
$stmt->execute();

// stok +1, baru sekarang karena alatnya baru benar-benar dipegang petugas
$stmt = $conn->prepare("UPDATE alat SET stok = stok + 1 WHERE id = ?");
$stmt->bind_param('i', $pjm['alat_id']);
$stmt->execute();

// catat di log siapa petugas yang menerima
$pesan = "Mengonfirmasi penerimaan alat '{$pjm['alat']}' dari {$pjm['username']}" . ($denda > 0 ? ", denda: Rp " . number_format($denda, 0, ',', '.') : "");
$stmt = $conn->prepare("INSERT INTO log_aktifitas (user_id, aksi) VALUES (?, ?)");
$stmt->bind_param('is', $petugas_id, $pesan);
$stmt->execute();

// balik ke daftar terima + notif sukses
header('location: terima.php?status=success');
exit;
?>
