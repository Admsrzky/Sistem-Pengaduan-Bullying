<?php

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['register-validate'])) {
    // Memulai blok try-catch untuk penanganan error yang lebih terstruktur dan rapi.
    try {
        $nama       = trim($_POST['nama'] ?? '');
        $nis_nip    = trim($_POST['nis_nip'] ?? '');
        $password   = $_POST['password'] ?? ''; // Kata sandi mentah (belum di-hash)
        $confirm    = $_POST['password_confirm'] ?? ''; // Konfirmasi kata sandi
        $role       = 'siswa'; // Role default untuk pengguna baru
        $created_at = date("Y-m-d H:i:s"); // Timestamp waktu pembuatan
        $updated_at = $created_at; // Timestamp waktu pembaruan (sama saat pertama kali dibuat)


        if (empty($nama) || empty($nis_nip) || empty($password) || empty($confirm)) {
            $_SESSION['register_status'] = 'empty'; // Pesan status untuk input kosong
            $_SESSION['active_tab'] = 'register'; // Pastikan tab register tetap aktif
            // Penting: Selalu gunakan header('Location: ...'); exit(); untuk redirect setelah set session.
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Memastikan kata sandi dan konfirmasi kata sandi cocok.
        if ($password !== $confirm) {
            $_SESSION['register_status'] = 'mismatch'; // Pesan status jika kata sandi tidak cocok
            $_SESSION['active_tab'] = 'register'; // Pastikan tab register tetap aktif
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // Tambahan: Validasi panjang kata sandi untuk keamanan yang lebih baik.
        // Disarankan minimal 8 karakter.
        if (strlen($password) < 8) {
            $_SESSION['register_status'] = 'password_too_short'; // Pesan status jika kata sandi terlalu pendek
            $_SESSION['active_tab'] = 'register'; // Pastikan tab register tetap aktif
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // --- LOGIKA BARU UNTUK VALIDASI KATA SANDI ---
        // Memeriksa apakah kata sandi mengandung setidaknya satu huruf besar dan satu angka
        if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $_SESSION['register_status'] = 'password_complexity'; // Pesan status untuk kerumitan kata sandi
            $_SESSION['active_tab'] = 'register'; // Pastikan tab register tetap aktif
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        // --- AKHIR LOGIKA BARU ---

        $queryCekNisNip = $conn->prepare("SELECT id FROM users WHERE nis_nip = ? LIMIT 1");
        if (!$queryCekNisNip) {
            // Jika persiapan query gagal, lempar Exception.
            throw new Exception("Gagal menyiapkan pengecekan NIS/NIP: " . $conn->error);
        }
        $queryCekNisNip->bind_param("s", $nis_nip); // 's' menandakan bahwa $nis_nip adalah string.
        $queryCekNisNip->execute(); // Jalankan query
        $hasilCekNisNip = $queryCekNisNip->get_result(); // Ambil hasilnya

        if ($hasilCekNisNip->num_rows > 0) {
            $_SESSION['register_status'] = 'exists'; // Pesan status jika NIS/NIP sudah ada
            $_SESSION['active_tab'] = 'register'; // Pastikan tab register tetap aktif
            $queryCekNisNip->close(); // Sangat penting: Tutup statement setelah digunakan.
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit();
        }
        $queryCekNisNip->close(); // Tutup statement jika NIS/NIP belum ada

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        if ($hashedPassword === false) {
            // Jika proses hashing gagal (jarang terjadi, tapi bisa karena masalah memori dll.), lempar Exception.
            throw new Exception("Gagal memproses kata sandi. Silakan coba lagi.");
        }

        // 5. Simpan Data Pengguna ke Tabel 'users'
        // Menggunakan Prepared Statement untuk query INSERT (sangat penting untuk keamanan).
        $queryInsertUser = $conn->prepare("INSERT INTO users (nis_nip, password, role, nama, foto_profile, created_at, updated_at) VALUES (?, ?, ?, ?, NULL, ?, ?)");
        if (!$queryInsertUser) {
            throw new Exception("Gagal menyiapkan penyimpanan data pengguna: " . $conn->error);
        }

        $queryInsertUser->bind_param("ssssss", $nis_nip, $hashedPassword, $role, $nama, $created_at, $updated_at);

        if ($queryInsertUser->execute()) {
            // Jika penyimpanan pengguna berhasil, ambil ID pengguna yang baru dibuat.
            $user_id = $conn->insert_id; // Mengambil ID dari record yang baru saja diinsert

            // 6. Simpan Data Siswa ke Tabel 'siswa' (jika role-nya 'siswa')
            // Menggunakan Prepared Statement lagi untuk keamanan.
            $queryInsertSiswa = $conn->prepare("INSERT INTO siswa (user_id, kelas_id, jurusan_id) VALUES (?, NULL, NULL)");
            if (!$queryInsertSiswa) {
                throw new Exception("Gagal menyiapkan penyimpanan data siswa: " . $conn->error);
            }
            $queryInsertSiswa->bind_param("i", $user_id); // 'i' menandakan $user_id adalah integer.
            $queryInsertSiswa->execute(); // Jalankan query
            $queryInsertSiswa->close(); // Tutup statement siswa

            $_SESSION['register_status'] = 'success'; // Pesan sukses pendaftaran
            $_SESSION['active_tab'] = 'login'; // Arahkan pengguna ke tab login
        } else {
            // Jika penyimpanan data pengguna gagal, lempar Exception.
            throw new Exception("Terjadi kesalahan saat menyimpan data pendaftaran: " . $queryInsertUser->error);
        }
        $queryInsertUser->close(); // Tutup statement pengguna setelah selesai

    } catch (Exception $e) {
        // Tangkap semua Exception yang mungkin terjadi dan simpan pesan errornya di session.
        $_SESSION['register_status'] = 'error'; // Pesan status error umum
        $_SESSION['error_message'] = $e->getMessage(); // Simpan pesan error yang lebih detail untuk debugging
        $_SESSION['active_tab'] = 'register'; // Pastikan tab register tetap aktif
    } finally {
        // Redirect selalu dilakukan di sini untuk memastikan session status dibawa.
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}
