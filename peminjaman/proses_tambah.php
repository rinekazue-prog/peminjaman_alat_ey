<?php
// simpan pengajuan dengan status pending, tanggal dicek lagi di server, lalu catat ke log
session_start();

// admin & peminjam boleh mengajukan, petugas tidak
if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'peminjam'])) {
    header('location: ../login.php');
    exit;
}

include '../config/connection.php';

$role = $_SESSION['role'];
$aid  = (int) ($_POST['alat_id'] ?? 0);

// halaman form asal, dipakai buat balik kalau ada error
$form_asal = ($role == 'peminjam') ? '../alat/pinjam.php?alat_id=' . $aid : 'tambah.php';

// bukan POST atau token salah = ditolak
if($_SERVER['REQUEST_METHOD'] !== 'POST'
   || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $_SESSION['error'] = 'Permintaan tidak valid, silakan ulangi.';
    header('location: ' . $form_asal);
    exit;
}

// peminjam dipaksa pakai id sendiri, peminjam_id dari form diabaikan
if($role == 'peminjam') {
    $pid = (int) $_SESSION['id'];
} else {
    $pid = (int) ($_POST['peminjam_id'] ?? 0);
}

$tgl   = $_POST['tgl_pinjam'] ?? '';
$tempo = $_POST['tgl_jatuh_tempo'] ?? '';
$hari_ini = date('Y-m-d');
$MAX_HARI = 7;  // lama pinjam maksimal (hari)

// fungsi kecil: simpan pesan error lalu balik ke form
function gagal($pesan, $tujuan) {
    $_SESSION['error'] = $pesan;
    header('location: ' . $tujuan);
    exit;
}

// format tanggal harus valid (YYYY-MM-DD)
$d1 = DateTime::createFromFormat('Y-m-d', $tgl);
$d2 = DateTime::createFromFormat('Y-m-d', $tempo);
if(!$d1 || $d1->format('Y-m-d') !== $tgl || !$d2 || $d2->format('Y-m-d') !== $tempo) {
    gagal('Format tanggal tidak valid!', $form_asal);
}
if($tgl < $hari_ini)  { gagal('Tanggal pinjam tidak boleh di masa lalu!', $form_asal); }
if($tempo <= $tgl)    { gagal('Tanggal jatuh tempo harus setelah tanggal pinjam!', $form_asal); }

$lama = (strtotime($tempo) - strtotime($tgl)) / 86400;
if($lama > $MAX_HARI) { gagal("Lama peminjaman maksimal $MAX_HARI hari!", $form_asal); }

// peminjam harus ada
$cu = $conn->prepare("SELECT username FROM users WHERE id = ?");
$cu->bind_param('i', $pid);
$cu->execute();
$usr = $cu->get_result()->fetch_assoc();
if(!$usr) { gagal('Peminjam tidak ditemukan!', $form_asal); }

// alat harus ada dan stoknya masih ada
$cek = $conn->prepare("SELECT nama, stok FROM alat WHERE id = ?");
$cek->bind_param('i', $aid);
$cek->execute();
$alat = $cek->get_result()->fetch_assoc();
if(!$alat || $alat['stok'] < 1) {
    $_SESSION['error'] = 'Alat tidak tersedia!';
    header('location: ' . ($role == 'peminjam' ? '../alat/index.php' : 'tambah.php'));
    exit;
}

// simpan: status awal pending, denda 0
$stmt = $conn->prepare("INSERT INTO peminjaman (peminjam_id, alat_id, tgl_pinjam, tgl_jatuh_tempo, status, denda) VALUES (?, ?, ?, ?, 'pending', 0)");
$stmt->bind_param('iiss', $pid, $aid, $tgl, $tempo);
$stmt->execute();

// catat di log: "Ajukan peminjaman ..." (hosting tanpa trigger, jadi ditulis di sini)
$pesan = "Ajukan peminjaman alat '{$alat['nama']}'";
if($role == 'admin') { $pesan .= " untuk {$usr['username']}"; }
catat_log($conn, (int) $_SESSION['id'], $pesan);

if($role == 'peminjam') {
    $_SESSION['success'] = 'Pengajuan peminjaman terkirim. Tunggu persetujuan petugas.';
    header('location: ../alat/index.php');
} else {
    $_SESSION['success'] = 'Peminjaman berhasil ditambahkan.';
    header('location: index.php');
}
exit;
?>
