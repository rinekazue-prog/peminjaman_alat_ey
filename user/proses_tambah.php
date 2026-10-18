<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if($username === '' || $password === '' || !in_array($role, ['admin', 'petugas', 'peminjam'])) {
    $_SESSION['error'] = 'Data user belum lengkap atau role tidak valid.';
    header('location: index.php');
    exit;
}

// username tidak boleh kembar
$cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
$cek->bind_param('s', $username);
$cek->execute();
if($cek->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'Username sudah dipakai. Pilih yang lain.';
    header('location: index.php');
    exit;
}

// password disimpan sebagai hash, bukan teks biasa
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $hash, $role);

try {
    $ok = $stmt->execute();
} catch (Exception $e) {
    $ok = false;
}

if(!$ok) {
    $_SESSION['error'] = 'User gagal disimpan. Pastikan kolom password sudah selebar 255 karakter (jalankan migrasi_password.php).';
}

header('location: index.php');
exit;
?>
