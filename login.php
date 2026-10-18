<?php 
session_start(); 
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
    <title>Masuk | Peminjaman Alat</title>
    <link rel="stylesheet" href="assets/css/tampil.css">
</head>
<body class="auth">
<main class="auth-box">
    <span class="brand">Peminjaman Alat</span>
    <h1>Masuk</h1>

    <?php if(isset($_SESSION['sukses'])): ?>
        <p class="notice notice-ok" role="status"><?= $_SESSION['sukses']; unset($_SESSION['sukses']); ?></p>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <p class="notice notice-error" role="alert"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
    <?php endif; ?>

    <form action="proses_login.php" method="POST">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" autofocus required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-primary btn-block">Masuk</button>
    </form>

    <p class="auth-alt">Belum punya akun? <a href="register.php">Daftar</a></p>
</main>
</body>
</html>
