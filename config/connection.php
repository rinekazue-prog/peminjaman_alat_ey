<?php
// di-include hampir semua halaman
$host = "sql305.infinityfree.com";
$user = "if0_43208907";
$pass = "K07QAgLXDq15vIC";
$db   = "if0_43208907_peminjaman";

// buat koneksi
$conn = new mysqli($host, $user, $pass, $db);

// koneksi gagal, tidak ada gunanya lanjut
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// helper escape output (cegah XSS), dipakai di view: echo e($data)
function e($s) {
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

// ---------- paginasi: 10 data per halaman ----------
// $sql = query lengkap (boleh pakai ORDER BY), $tipe/$params = bind_param untuk tanda tanya di query
// hasil: [data halaman ini, halaman aktif, total halaman, total data, jumlah data yg dilewati]
function paginate($conn, $sql, $tipe = '', $params = [], $per = 10) {
    // hitung total data
    $stmt = $conn->prepare("SELECT COUNT(*) AS n FROM ($sql) AS t");
    if($params) { $stmt->bind_param($tipe, ...$params); }
    $stmt->execute();
    $total = (int) $stmt->get_result()->fetch_assoc()['n'];

    // halaman dari URL (?page=2), dijaga supaya tidak di luar batas
    $pages  = max(1, (int) ceil($total / $per));
    $page   = min(max(1, (int) ($_GET['page'] ?? 1)), $pages);
    $offset = ($page - 1) * $per;

    // ambil 10 data untuk halaman ini
    $stmt = $conn->prepare($sql . " LIMIT " . (int) $per . " OFFSET " . (int) $offset);
    if($params) { $stmt->bind_param($tipe, ...$params); }
    $stmt->execute();

    return [$stmt->get_result(), $page, $pages, $total, $offset];
}

// link nomor halaman (maksimal 10 nomor), kata pencarian ?q= ikut terbawa
function tampil_halaman($page, $pages) {
    if($pages <= 1) { return; }
    $link = function($p) {
        $query = $_GET;
        $query['page'] = $p;
        return '?' . htmlspecialchars(http_build_query($query), ENT_QUOTES, 'UTF-8');
    };
    echo '<p>';
    if($page > 1) { echo '<a href="' . $link($page - 1) . '">&laquo; Sebelumnya</a> '; }
    $mulai = max(1, min($page - 4, $pages - 9));
    $akhir = min($pages, $mulai + 9);
    for($i = $mulai; $i <= $akhir; $i++) {
        if($i == $page) { echo '<strong>' . $i . '</strong> '; }
        else { echo '<a href="' . $link($i) . '">' . $i . '</a> '; }
    }
    if($page < $pages) { echo '<a href="' . $link($page + 1) . '">Berikutnya &raquo;</a>'; }
    echo '</p>';
}

// ---------- catat log aktivitas ----------
// hosting gratis tidak mendukung trigger, jadi log ditulis lewat PHP
// kolom aksi cuma 100 karakter, jadi dipotong supaya tidak error
function catat_log($conn, $user_id, $aksi) {
    $aksi = mb_substr($aksi, 0, 100);
    $stmt = $conn->prepare("INSERT INTO log_aktifitas (user_id, aksi) VALUES (?, ?)");
    $stmt->bind_param('is', $user_id, $aksi);
    $stmt->execute();
}

// token anti-CSRF: dibuat sekali per sesi
function csrf_token() {
    if(empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
?>
