<?php
session_start();

// Simpan role pengguna sebelum sesi dihancurkan
// Ini penting karena kita akan memerlukannya untuk redirect yang berbeda
$user_role = $_SESSION['role'] ?? null;

// Hapus semua variabel sesi
$_SESSION = [];

// Hancurkan sesi
session_unset();
session_destroy();

// Tentukan URL redirect berdasarkan role pengguna
$redirect_url = 'home.php'; // Default ke halaman beranda/landing page

if ($user_role === 'admin' || $user_role === 'kepsek') {
    $redirect_url = 'login.php'; // Admin and Kepsek will redirect to the login page
}
// Jika role adalah 'siswa' atau 'guru', akan tetap ke index.php (beranda)

// Lakukan redirect
header('Location: ' . $redirect_url);
exit(); // Pastikan tidak ada kode lain yang dieksekusi setelah redirect