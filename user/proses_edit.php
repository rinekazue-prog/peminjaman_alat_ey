<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('location: ../login.php');
    exit;
}
include '../config/connection.php';

$id       = (int) ($_POST['id'] ?? 0);
$username = trim($_POST['username'] ?? '');
$role     = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';

if($username === '' || !in_array($role, ['admin', 'petugas', 'peminjam'])) {
    $_SESSION['error'] = 'Data user belum lengkap atau role tidak valid.';
    header('location: index.php');
    exit;
}

// username tidak boleh sama dengan user lain
$cek = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
$cek->bind_param('si', $username, $id);
$cek->execute();
if($cek->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'Username sudah dipakai user lain.';
    header('location: index.php');
    exit;
}

if(!empty($password)) {
    // password diisi, berarti diganti (disimpan sebagai hash)
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, role = ? WHERE id = ?");
    $stmt->bind_param('sssi', $username, $hash, $role, $id);
} else {
    $stmt = $conn->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
    $stmt->bind_param('ssi', $username, $role, $id);
}

try {
    $ok = $stmt->execute();
} catch (Exception $e) {
    $ok = false;
}

if(!$ok) {
    $_SESSION['error'] = 'User gagal diperbarui. Pastikan kolom password sudah selebar 255 karakter (jalankan migrasi_password.php).';
}

header('location: index.php');
exit;
?>
