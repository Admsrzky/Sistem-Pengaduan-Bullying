<?php
session_start(); // Pastikan session sudah dimulai

include 'config/database.php'; // Pastikan file ini menghubungkan ke database dan membuat variabel $conn

// 1. Periksa Status Login Pengguna
// Jika pengguna belum login (session nis_nip tidak ada), arahkan ke halaman login.
if (!isset($_SESSION['nis_nip'])) {
    header("Location: login.php");
    exit();
}

// Periksa apakah permintaan datang dari metode POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Jika bukan POST, arahkan kembali atau tampilkan error
    header("Location: account-setting.php");
    exit();
}

// 2. Ambil Input dari Formulir dan Bersihkan
// TIDAK LAGI menggunakan mysqli_real_escape_string untuk input yang akan diikat ke prepared statement.
// Gunakan trim() untuk membersihkan spasi ekstra.
$nis_nip_session = trim($_SESSION['nis_nip']); // NIS/NIP dari sesi yang sudah pasti aman
$old_password    = $_POST['old_password'] ?? '';
$new_password    = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Memulai blok try-catch untuk penanganan error yang lebih terstruktur.
try {
    // 3. Ambil Data Pengguna dari Database
    // Menggunakan Prepared Statement untuk mencari pengguna berdasarkan NIS/NIP.
    $queryUser = $conn->prepare("SELECT id, password FROM users WHERE nis_nip = ? LIMIT 1");
    if (!$queryUser) {
        throw new Exception("Gagal menyiapkan query pengguna: " . $conn->error);
    }
    $queryUser->bind_param("s", $nis_nip_session); // 's' menandakan nis_nip adalah string
    $queryUser->execute();
    $resultUser = $queryUser->get_result();
    $user = $resultUser->fetch_assoc();
    $queryUser->close(); // Tutup statement setelah digunakan

    // Periksa apakah pengguna ditemukan
    if (!$user) {
        throw new Exception("Pengguna tidak ditemukan.");
    }

    if (!password_verify($old_password, $user['password'])) {
        throw new Exception("Kata sandi lama tidak sesuai.");
    }

    if ($new_password !== $confirm_password) {
        throw new Exception("Konfirmasi kata sandi baru tidak cocok.");
    }

    // Tambahan: Validasi panjang kata sandi baru (disarankan minimal 8 karakter)
    if (strlen($new_password) < 8) {
        throw new Exception("Kata sandi baru terlalu pendek. Minimal 8 karakter.");
    }

    // 6. Hash Kata Sandi Baru
    // Menggunakan password_hash() dengan algoritma PASSWORD_DEFAULT untuk keamanan.
    $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    if ($new_password_hash === false) {
        throw new Exception("Gagal memproses kata sandi baru.");
    }

    // 7. Perbarui Kata Sandi di Database
    // Menggunakan Prepared Statement untuk query UPDATE.
    $updateQuery = $conn->prepare("UPDATE users SET password = ?, updated_at = ? WHERE nis_nip = ?");
    if (!$updateQuery) {
        throw new Exception("Gagal menyiapkan update kata sandi: " . $conn->error);
    }
    $current_time = date("Y-m-d H:i:s"); // Dapatkan waktu saat ini untuk updated_at
    // 'sss' menandakan semua parameter adalah string: new_password_hash, current_time, nis_nip_session
    $updateQuery->bind_param("sss", $new_password_hash, $current_time, $nis_nip_session);

    if ($updateQuery->execute()) {
        $_SESSION['success'] = "Kata sandi berhasil diubah.";
    } else {
        throw new Exception("Gagal mengubah kata sandi: " . $updateQuery->error);
    }
    $updateQuery->close(); // Tutup statement update

} catch (Exception $e) {
    // Tangkap semua error dan simpan pesan error ke session
    $_SESSION['error'] = $e->getMessage();
} finally {
    // Pastikan koneksi database ditutup
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
}

// Arahkan kembali ke halaman pengaturan akun
header("Location: account-setting.php");
exit();