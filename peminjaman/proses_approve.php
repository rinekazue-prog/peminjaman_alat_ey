<?php
// setujui: status jadi 'disetujui' dan stok alat berkurang 1 (hanya kalau stok masih ada)
// tolak: cuma ganti status, stok tidak berubah
// dipanggil lewat POST dari approve.php
session_start();
// hanya petugas, role lain dilempar ke login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

// bukan POST atau token salah = ditolak (cegah aksi lewat link / CSRF)
if($_SERVER['REQUEST_METHOD'] !== 'POST'
   || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
    $_SESSION['error'] = 'Permintaan tidak valid, silakan ulangi.';
    header('location: approve.php');
    exit;
}

// id peminjaman + aksi (setujui / tolak) dari form
$id         = (int) ($_POST['id'] ?? 0);
$aksi       = $_POST['aksi'] ?? '';
$petugas_id = (int) $_SESSION['id'];

// ambil data pengajuan + nama peminjam & alat (buat pesan log)
// hanya yang masih 'pending', jadi tidak bisa diproses dua kali
$stmt = $conn->prepare("SELECT p.alat_id, u.username, a.nama AS alat
                        FROM peminjaman p
                        JOIN users u ON p.peminjam_id = u.id
                        JOIN alat a ON p.alat_id = a.id
                        WHERE p.id = ? AND p.status = 'pending'");
$stmt->bind_param('i', $id);
$stmt->execute();
$pjm = $stmt->get_result()->fetch_assoc();

if(!$pjm) {
    $_SESSION['error'] = 'Pengajuan tidak ditemukan atau sudah diproses.';
    header('location: approve.php');
    exit;
}

if($aksi == 'setujui') {
    // transaksi: stok & status harus berhasil dua-duanya, atau batal semua
    $conn->begin_transaction();
    try {
        // stok -1 HANYA kalau stok > 0 (cek + kurangi dalam satu query, aman dari rebutan)
        $stmt = $conn->prepare("UPDATE alat SET stok = stok - 1 WHERE id = ? AND stok > 0");
        $stmt->bind_param('i', $pjm['alat_id']);
        $stmt->execute();

        if($stmt->affected_rows === 0) {
            // stok sudah habis, batalkan
            $conn->rollback();
            $_SESSION['error'] = "Stok '{$pjm['alat']}' habis, peminjaman tidak bisa disetujui.";
            header('location: approve.php');
            exit;
        }

        // ubah status (tetap dijaga: hanya kalau masih pending)
        $stmt = $conn->prepare("UPDATE peminjaman SET status = 'disetujui' WHERE id = ? AND status = 'pending'");
        $stmt->bind_param('i', $id);
        $stmt->execute();

        // catat petugas yang menyetujui (trigger db cuma mencatat perubahan status atas nama peminjam)
        $pesan = mb_substr("Menyetujui peminjaman '{$pjm['alat']}' oleh {$pjm['username']}", 0, 100);
        $stmt = $conn->prepare("INSERT INTO log_aktifitas (user_id, aksi) VALUES (?, ?)");
        $stmt->bind_param('is', $petugas_id, $pesan);
        $stmt->execute();

        $conn->commit();
        $_SESSION['success'] = "Peminjaman '{$pjm['alat']}' oleh {$pjm['username']} disetujui.";
    } catch (Throwable $e) {
        $conn->rollback();
        $_SESSION['error'] = 'Gagal memproses persetujuan, silakan coba lagi.';
    }

} else if($aksi == 'tolak') {
    // tolak: ganti status saja, stok tidak disentuh
    $stmt = $conn->prepare("UPDATE peminjaman SET status = 'ditolak' WHERE id = ? AND status = 'pending'");
    $stmt->bind_param('i', $id);
    $stmt->execute();

    // catat petugas yang menolak
    $pesan = mb_substr("Menolak peminjaman '{$pjm['alat']}' oleh {$pjm['username']}", 0, 100);
    $stmt = $conn->prepare("INSERT INTO log_aktifitas (user_id, aksi) VALUES (?, ?)");
    $stmt->bind_param('is', $petugas_id, $pesan);
    $stmt->execute();

    $_SESSION['success'] = "Peminjaman '{$pjm['alat']}' oleh {$pjm['username']} ditolak.";
}

// balik ke daftar pengajuan
header('location: approve.php');
exit;
?>
