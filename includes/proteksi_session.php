<?php
// Proteksi session untuk halaman admin
if(!isset($_SESSION)) {
    session_start();
}

// Cek apakah user sudah login
if(!isset($_SESSION['id_user'])) {
    header('Location: ../login.php');
    exit();
}

// Cek apakah user adalah admin
if(!isset($_SESSION['peran']) || $_SESSION['peran'] != 'admin') {
    header('Location: ../index.php');
    exit();
}
?>