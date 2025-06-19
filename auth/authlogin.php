<?php
// Pastikan session_start() sudah terpanggil di file utama yang meng-include ini
// include 'config/database.php'; // Ini seharusnya di-include di file utama

// --- DEBUGGING: Aktifkan error reporting untuk melihat pesan kesalahan PHP ---
// HANYA UNTUK DEVELOPMENT, JANGAN DI PRODUKSI
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -------------------------------------------------------------------------

// Redirect user jika sudah login (tetap sama)
if (isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'kepsek':
        case 'admin':
            header('Location: views/admin/dashboard-admin.php');
            exit();
        case 'siswa':
        case 'guru':
            header('Location: index.php');
            exit();
    }
}

$loginStatus = ''; // Inisialisasi status di sini

if (isset($_POST['login-validate'])) {
    error_log("--- Proses Login Dimulai ---");
    try {
        // 1. Ambil dan Bersihkan Input Pengguna
        $nis_nip = trim($_POST['nis_nip'] ?? '');
        $password = $_POST['password'] ?? '';

        error_log("Input NIS/NIP: " . $nis_nip);
        error_log("Input Password (plain): " . $password);


        // 2. Validasi Input Dasar
        if (empty($nis_nip) || empty($password)) {
            $loginStatus = 'empty';
            error_log("Validasi Input: NIS/NIP atau password kosong.");
            throw new Exception("NIS/NIP dan kata sandi wajib diisi.");
        }

        // Pastikan $conn tersedia dari file yang meng-include authlogin.php ini
        if (!isset($conn)) {
            throw new Exception("Koneksi database tidak tersedia.");
        }


        // 3. Cari Pengguna di Database (Menggunakan Prepared Statement untuk keamanan)
        $queryUser = $conn->prepare("SELECT id, nis_nip, password, role, nama, foto_profile FROM users WHERE nis_nip = ? LIMIT 1");
        if (!$queryUser) {
            error_log("Gagal menyiapkan query pengguna: " . $conn->error);
            throw new Exception("Gagal menyiapkan query pengguna: " . $conn->error);
        }

        $queryUser->bind_param("s", $nis_nip);
        if (!$queryUser->execute()) {
            error_log("Gagal mengeksekusi query pengguna: " . $queryUser->error);
            throw new Exception("Gagal mengeksekusi query pengguna: " . $queryUser->error);
        }

        $resultUser = $queryUser->get_result();

        // 4. Periksa Hasil Query
        if ($resultUser && $resultUser->num_rows > 0) {
            $user = $resultUser->fetch_assoc();
            $queryUser->close();

            error_log("Pengguna ditemukan. Data user dari DB: " . json_encode($user));
            error_log("Password dari form (plain): " . $password);
            error_log("Hash password dari DB: " . $user['password']);

            // 5. Verifikasi Kata Sandi Hashed
            if (password_verify($password, $user['password'])) {
                // Kata sandi cocok, buat sesi login
                $_SESSION['user_logged_in'] = true;
                $_SESSION['role'] = $user['role'];
                $_SESSION['id'] = $user['id'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['nis_nip'] = $user['nis_nip'];
                $_SESSION['foto_profile'] = $user['foto_profile'];

                // Tambahan: Ambil data spesifik role (misal: siswa)
                if ($user['role'] === 'siswa') {
                    $querySiswa = $conn->prepare("SELECT kelas_id, jurusan_id FROM siswa WHERE user_id = ? LIMIT 1");
                    if (!$querySiswa) {
                        error_log("Gagal menyiapkan query siswa: " . $conn->error);
                        throw new Exception("Gagal menyiapkan query siswa: " . $conn->error);
                    }
                    $querySiswa->bind_param("i", $user['id']);
                    if (!$querySiswa->execute()) {
                        error_log("Gagal mengeksekusi query siswa: " . $querySiswa->error);
                        throw new Exception("Gagal mengeksekusi query siswa: " . $querySiswa->error);
                    }
                    $resultSiswa = $querySiswa->get_result();

                    if ($resultSiswa && $resultSiswa->num_rows > 0) {
                        $siswa = $resultSiswa->fetch_assoc();
                        $_SESSION['kelas_id'] = $siswa['kelas_id'];
                        $_SESSION['jurusan_id'] = $siswa['jurusan_id'];
                    }
                    $querySiswa->close();
                }

                $loginStatus = 'success';
                $_SESSION['login_redirect'] = $user['role'];
                error_log("Login BERHASIL. Role: " . $user['role']);
            } else {
                $loginStatus = 'wrong_password';
                error_log("Login GAGAL: password_verify() false. Input password: '$password', DB hash: '{$user['password']}'");
                throw new Exception("Kata sandi salah.");
            }
        } else {
            $loginStatus = 'not_found';
            error_log("Login GAGAL: NIS/NIP '$nis_nip' tidak ditemukan di database.");
            throw new Exception("NIS/NIP tidak ditemukan.");
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        if ($loginStatus === '') {
            $loginStatus = 'error_general';
        }
        error_log("Login GAGAL (Exception): " . $e->getMessage());
    } finally {
        // Koneksi database harus ditutup di file utama atau setelah semua operasi DB selesai
        // if (isset($conn) && $conn->ping()) {
        //      $conn->close();
        // }
        error_log("--- Proses Login Selesai ---");
    }
}