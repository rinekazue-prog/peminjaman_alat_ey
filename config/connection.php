<?php
// koneksi mysqli ke database, hasilnya di variabel $conn
// di-include hampir semua halaman
$host = "localhost";
$user = "root";
$pass = "";
$db   = "alfinrano_xiirpl_paket1";

// buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// koneksi gagal, tidak ada gunanya lanjut
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
