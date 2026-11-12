<?php 
// form daftar akun (publik). akun baru otomatis jadi peminjam
session_start(); 
// sudah login, tidak perlu lihat halaman ini lagi
if(isset($_SESSION['role'])) { 
    header('location: index.php'); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | Peminjaman Alat</title>
    <link rel="stylesheet" href="assets/css/tampil.css?v=2">
</head>
<body class="auth">
<main class="auth-box">
    <span class="brand">Peminjaman Alat</span>
    <h1>Daftar</h1>

    <!-- flash error: tampil sekali, langsung di-unset -->
    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <!-- submit ke proses_register.php -->
    <form action="proses_register.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" minlength="3" autofocus required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" minlength="6" required>
        <span class="hint">Minimal 6 karakter.</span>

        <label for="ulangi">Ulangi password</label>
        <input type="password" id="ulangi" name="ulangi" minlength="6" required>

        <button type="submit" class="btn btn-primary btn-block">Buat akun</button>
    </form>

    <p class="auth-alt">Sudah punya akun? <a href="login.php">Masuk</a></p>
</main>
</body>
</html>
