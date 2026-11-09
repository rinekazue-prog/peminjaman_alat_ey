<?php
// proses daftar: validasi input, cek username belum dipakai, simpan dengan password di-hash
// role dikunci 'peminjam' di sini (tidak dari form) supaya orang tidak bisa daftar jadi admin
session_start();
include 'config/connection.php';

// balik ke form pendaftaran sambil bawa pesan error
function gagal($pesan) {
    $_SESSION['error'] = $pesan;
    header('location: register.php');
    exit;
}

// data dari form
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$ulangi   = $_POST['ulangi'] ?? '';

// validasi: panjang minimal, dan ulangi password harus sama
if(strlen($username) < 3) {
    gagal('Username minimal 3 karakter.');
}
if(strlen($password) < 6) {
    gagal('Password minimal 6 karakter.');
}
if($password !== $ulangi) {
    gagal('Ulangi password tidak sama dengan password.');
}

// username tidak boleh kembar
$cek = $conn->prepare("SELECT id FROM users WHERE username = ?");
$cek->bind_param('s', $username);
$cek->execute();
if($cek->get_result()->num_rows > 0) {
    gagal('Username sudah dipakai. Pilih yang lain.');
}

// akun daftar sendiri selalu jadi peminjam, role tidak diambil dari form
$hash = password_hash($password, PASSWORD_DEFAULT);
// simpan ke db
$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, 'peminjam')");
$stmt->bind_param('ss', $username, $hash);

// execute bisa melempar exception, ditangkap biar tidak muncul error mentah
try {
    $ok = $stmt->execute();
} catch (Exception $e) {
    $ok = false;
}

// gagal: titip pesan error lewat session
if(!$ok) {
    gagal('Pendaftaran gagal. Coba lagi nanti.');
}

// pesan sukses tampil di login.php
$_SESSION['sukses'] = 'Akun berhasil dibuat. Silakan masuk.';
// ke login
header('location: login.php');
exit;
?>
