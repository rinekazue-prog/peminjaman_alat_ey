<?php
// peminjam mengajukan pengembalian: status jadi 'menunggu_konfirmasi', tanggal ajukan dicatat
// alat belum dianggap kembali sampai petugas menerimanya (lihat proses_terima.php)
session_start();
// hanya peminjam, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'peminjam') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// bukan POST atau token salah = ditolak (cegah aksi lewat link / CSRF)
if($_SERVER['REQUEST_METHOD'] !== 'POST'
   || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $_SESSION['error'] = 'Permintaan tidak valid, silakan ulangi.';
    header('location: ../alat/index.php');
    exit;
}

// id dari form, di-cast int biar aman
$id = (int) ($_POST['id'] ?? 0);
// id user yang login
$user_id = (int) $_SESSION['id'];
// hari ini = tanggal peminjam mengajukan pengembalian
$tgl_ajukan = date('Y-m-d');

// pastikan peminjaman ini milik dia dan masih 'disetujui' (cegah ajukan punya orang lain / dobel)
$stmt = $conn->prepare("SELECT * FROM peminjaman WHERE id = ? AND peminjam_id = ? AND status = 'disetujui'");
$stmt->bind_param('ii', $id, $user_id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

// data tidak valid atau sudah diproses, stop
if(!$pjm) {
    $_SESSION['error'] = 'Data peminjaman tidak ditemukan atau sudah diproses.';
    header('location: ../alat/index.php');
    exit;
}

// tandai pengajuan pengembalian. alat BELUM dianggap kembali
// sampai petugas konfirmasi di pengembalian/terima.php
$stmt = $conn->prepare("UPDATE peminjaman SET status = 'menunggu_konfirmasi', tgl_ajukan_kembali = ? WHERE id = ?");
$stmt->bind_param('si', $tgl_ajukan, $id);
$stmt->execute();

// catat di log (hosting tanpa trigger, jadi ditulis langsung dari sini)
$ns = $conn->prepare("SELECT nama FROM alat WHERE id = ?");
$ns->bind_param('i', $pjm['alat_id']);
$ns->execute();
$nama_alat = $ns->get_result()->fetch_assoc()['nama'] ?? '';
catat_log($conn, $user_id, "Mengajukan pengembalian alat '{$nama_alat}'");

// balik ke Daftar alat + notif sukses
$_SESSION['success'] = 'Pengembalian sudah diajukan. Serahkan alatnya ke petugas, status berubah setelah petugas mengonfirmasi alat sudah diterima.';
header('location: ../alat/index.php');
exit;
?>
