<?php
session_start();
include 'config/connection.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// prepared statement, jadi input username aman dari SQL injection
$stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if($user) {
    // password lama masih teks biasa? berarti migrasi belum dijalankan
    if(!password_get_info($user['password'])['algo']) {
        $_SESSION['error'] = 'Password belum diamankan. Jalankan migrasi_password.php sekali, lalu coba lagi.';
        header('location: login.php');
        exit;
    }

    if(password_verify($password, $user['password'])) {
        session_regenerate_id(true);
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
