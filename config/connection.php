<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "alfinrano_xiirpl_paket1";

// Bikin koneksi
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>