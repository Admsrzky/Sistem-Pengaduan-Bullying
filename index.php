<?php
session_start();

// Halaman yang boleh diakses tanpa login
$publicPages = ['home', 'login', 'lapor-sukses'];

// Ambil halaman dari URL
$page = $_GET['page'] ?? 'home';

// Redirect ke login jika halaman butuh login dan belum login
if (!in_array($page, $publicPages) && !isset($_SESSION['user_logged_in'])) {
    header("Location: index.php?page=login");
    exit;
}
switch ($page) {
    case 'home':
        include 'home.php';
        break;
    case 'login':
        include 'login.php';
        break;
    case 'logout':
        include 'logout.php';
        break;
    case 'riwayat-laporan':
        include 'riwayat-laporan.php';
        break;
    case 'profile':
        include 'profile.php';
        break;
    case 'account-setting':
        include 'account-setting.php';
        break;
    case 'update-password':
        include 'update-password.php';
        break;
    case 'update-profile':
        include 'update-profile.php';
        break;
    case 'laporan':
        include 'laporan.php';
        break;
    case 'lapor-sukses':
        include 'lapor-sukses.php';
        break;
    case 'detail-laporan':
        include 'detail-laporan.php';
        break;
    default:
        echo "<h1>404 - Halaman tidak ditemukan</h1>";
        break;
}
