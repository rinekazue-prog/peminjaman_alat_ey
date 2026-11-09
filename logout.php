<?php
// logout: hapus session lalu balik ke login
session_start();
// buang semua data login
session_destroy();
// ke login
header('location: login.php');
?>