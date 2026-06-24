<?php
// Konfigurasi database
$host_db = 'localhost';
$nama_db = 'jogja_historia';
$user_db = 'root';
$pass_db = '';

// Koneksi ke database
$koneksi = mysqli_connect($host_db, $user_db, $pass_db, $nama_db);

// Cek koneksi
if(!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error() . "<br>Silakan jalankan <a href='instalasi.php'>instalasi.php</a> terlebih dahulu.");
}

// Set charset
mysqli_set_charset($koneksi, "utf8mb4");

// Konfigurasi website
define('NAMA_SITUS', 'Jogja Historia');
define('URL_SITUS', 'http://localhost/jogja-historia/');
define('URL_ASSET', URL_SITUS . 'assets/');

// Fungsi helper
function alihkan($url) {
    header("Location: $url");
    exit();
}

function sudah_login() {
    if(isset($_SESSION['id_user'])) {
        return true;
    }
    return false;
}

function adalah_admin() {
    if(isset($_SESSION['peran']) && $_SESSION['peran'] == 'admin') {
        return true;
    }
    return false;
}

function dapatkan_id_user() {
    if(isset($_SESSION['id_user'])) {
        return $_SESSION['id_user'];
    }
    return null;
}

function bersihkan_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function buat_slug($teks) {
    $teks = strtolower($teks);
    $teks = preg_replace('/[^a-z0-9\s-]/', '', $teks);
    $teks = preg_replace('/[\s-]+/', '-', $teks);
    $teks = trim($teks, '-');
    return $teks;
}
?>