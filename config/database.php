<?php
// 1. Konfigurasi Database
$host = 'localhost';
$db   = 'bullying_db';
$user = 'root';
$pass = '';

// 2. Membuat koneksi MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // Aktifkan exception untuk error
try {
    $conn = new mysqli($host, $user, $pass, $db);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    error_log("Koneksi Database Gagal: " . $e->getMessage());
    die("Tidak dapat terhubung ke database. Silakan hubungi administrator.");
}
