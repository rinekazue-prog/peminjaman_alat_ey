<?php
// proses login: cari user -> cocokkan password dengan hash -> simpan id, username, role ke session
// kalau gagal, pesan dititip lewat $_SESSION['error'] dan ditampilkan di login.php
// session = tempat status login disimpan (id, username, role)
session_start();
include 'config/connection.php';

// data dari form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// prepared statement, jadi input username aman dari SQL injection
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// username ada, lanjut cek password
if($user) {
    // password lama masih teks biasa? berarti migrasi belum dijalankan
    if(!password_get_info($user['password'])['algo']) {
        $_SESSION['error'] = 'Password belum diamankan. Jalankan migrasi_password.php sekali, lalu coba lagi.';
        header('location: login.php');
        exit;
    }

    // bandingkan dengan hash di db
    if(password_verify($password, $user['password'])) {
        // ganti session id setelah login (cegah session fixation)
        session_regenerate_id(true);
        // ini yang dicek halaman lain buat tahu user sudah login
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        header('location: index.php');
    } else {
        $_SESSION['error'] = 'Password salah. Coba lagi.';
        header('location: login.php');
    }
} else {
    $_SESSION['error'] = 'Username tidak ditemukan.';
    header('location: login.php');
}
exit;
?>
