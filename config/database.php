<?php
// File: config/database.php

// 1. Konfigurasi Database
$host = 'localhost'; // Sesuaikan host database
$db   = 'bullying_db'; // Sesuaikan nama database
$user = 'root';        // Sesuaikan username database
$pass = '';            // Sesuaikan password database
$charset = 'utf8mb4';

// 2. Data Source Name (DSN)
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// 3. Opsi untuk PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// 4. Membuat koneksi PDO
try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Jika koneksi gagal, catat error dan hentikan skrip
    error_log("Koneksi Database Gagal: " . $e->getMessage());
    // Pesan ini akan ditampilkan ke pengguna jika server error
    die("Tidak dapat terhubung ke database. Silakan hubungi administrator.");
}
