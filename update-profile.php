<?php
session_start();
include 'config/database.php'; // Koneksi ke DB

// Pastikan user sudah login
if (!isset($_SESSION['nis_nip'])) {
    header('Location: login.php');
    exit();
}

// Pastikan permintaan datang dari metode POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION['error'] = "Akses tidak valid.";
    header("Location: profile.php"); // Menggunakan profile.php untuk konsistensi
    exit();
}

try {
    $nis_nip_session = $_SESSION['nis_nip']; // NIS/NIP dari sesi

    // 1. Ambil data user saat ini dari database (termasuk role dan foto_profile)
    // PENTING: Pastikan Anda mengambil 'foto_profile' di query ini untuk mendapatkan nama file lama.
    $queryUser = $conn->prepare("SELECT id, foto_profile, role FROM users WHERE nis_nip = ? LIMIT 1");
    if (!$queryUser) {
        throw new Exception("Gagal menyiapkan query user: " . $conn->error);
    }
    $queryUser->bind_param("s", $nis_nip_session);
    $queryUser->execute();
    $resultUser = $queryUser->get_result();
    $user = $resultUser->fetch_assoc();
    $queryUser->close();

    if (!$user) {
        throw new Exception("Pengguna tidak ditemukan.");
    }

    $user_id = $user['id'];
    $current_photo = $user['foto_profile']; // <<== INI FOTO PROFIL LAMA DARI DATABASE
    $user_role = $user['role'];

    // Ambil data yang dikirim dari form
    $nama = trim($_POST['nama'] ?? '');

    // Validasi input nama
    if (empty($nama)) {
        throw new Exception("Nama lengkap tidak boleh kosong.");
    }

    $foto_profil_baru = $current_photo; // Default: jika tidak ada upload, pakai foto lama

    // 2. Penanganan Unggah Foto Profil (jika ada file baru diunggah)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        if (!in_array($file_ext, $allowed_extensions)) {
            throw new Exception("Format foto tidak didukung. Hanya JPG, JPEG, PNG, GIF yang diizinkan.");
        }

        if ($file_size > $max_file_size) {
            throw new Exception("Ukuran foto terlalu besar. Maksimal 2MB.");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $actual_mime_type = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($actual_mime_type, $allowed_mimes)) {
            throw new Exception("Tipe file tidak valid. Harap unggah file gambar yang sebenarnya.");
        }

        $new_file_name = uniqid('profile_') . '.' . $file_ext;
        $upload_dir = 'assets/img/profile/'; // Pastikan path ini benar dan dapat diakses/ditulis oleh web server
        $upload_path = $upload_dir . $new_file_name;

        // Pastikan direktori ada, jika tidak buat
        if (!is_dir($upload_dir)) {
            // Memberikan izin 0777 untuk development, sesuaikan di produksi
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Gagal membuat direktori unggahan.");
            }
        }

        // Pindahkan file yang diunggah dari lokasi sementara ke lokasi permanen
        if (move_uploaded_file($file_tmp, $upload_path)) {
            $foto_profil_baru = $new_file_name;

            // LOGIKA PENGHAPUSAN FOTO LAMA - SUDAH ADA DI KODE ANDA DAN KINI DIVERIFIKASI
            // Pastikan $current_photo bukan NULL/kosong dan bukan 'default.png'
            // dan file tersebut benar-benar ada sebelum mencoba menghapus.
            if (!empty($current_photo) && $current_photo !== 'default.png' && file_exists($upload_dir . $current_photo)) {
                unlink($upload_dir . $current_photo); // Hapus foto lama
                // Anda bisa menambahkan error_log di sini jika unlink gagal
            }
        } else {
            $upload_error_code = $_FILES['foto']['error'];
            $error_messages = [
                UPLOAD_ERR_INI_SIZE => "Foto melebihi batas ukuran yang diizinkan server.",
                UPLOAD_ERR_FORM_SIZE => "Foto melebihi batas ukuran yang ditentukan form.",
                UPLOAD_ERR_PARTIAL => "Foto hanya terunggah sebagian.",
                UPLOAD_ERR_NO_TMP_DIR => "Direktori sementara untuk unggahan hilang.",
                UPLOAD_ERR_CANT_WRITE => "Gagal menulis foto ke disk.",
                UPLOAD_ERR_EXTENSION => "Ekstensi PHP menghentikan unggahan foto."
            ];
            $error_description = $error_messages[$upload_error_code] ?? "Kode error tidak dikenal: " . $upload_error_code;
            throw new Exception("Gagal mengunggah foto profil. " . $error_description);
        }
    }

    // 3. Perbarui Data di Tabel 'users'
    $updateUserQuery = $conn->prepare("UPDATE users SET nama = ?, foto_profile = ?, updated_at = ? WHERE id = ?");
    if (!$updateUserQuery) {
        throw new Exception("Gagal menyiapkan update data pengguna: " . $conn->error);
    }
    $current_time = date("Y-m-d H:i:s");
    $updateUserQuery->bind_param("sssi", $nama, $foto_profil_baru, $current_time, $user_id);
    if (!$updateUserQuery->execute()) {
        throw new Exception("Gagal memperbarui data pengguna: " . $updateUserQuery->error);
    }
    $updateUserQuery->close();

    // Perbarui session nama dan foto_profile agar langsung terlihat di halaman
    $_SESSION['nama'] = $nama;
    $_SESSION['foto_profile'] = $foto_profil_baru;

    // 4. Perbarui Data Spesifik Berdasarkan Role
    if ($user_role === 'siswa') {
        $kelas_id = isset($_POST['kelas_id']) ? intval($_POST['kelas_id']) : null;
        $jurusan_id = isset($_POST['jurusan_id']) ? intval($_POST['jurusan_id']) : null;

        $checkSiswaQuery = $conn->prepare("SELECT id FROM siswa WHERE user_id = ? LIMIT 1");
        $checkSiswaQuery->bind_param("i", $user_id);
        $checkSiswaQuery->execute();
        $resultCheckSiswa = $checkSiswaQuery->get_result();
        $siswaExists = $resultCheckSiswa->num_rows > 0;
        $checkSiswaQuery->close();

        if ($siswaExists) {
            $updateSiswaQuery = $conn->prepare("UPDATE siswa SET kelas_id = ?, jurusan_id = ? WHERE user_id = ?");
            if (!$updateSiswaQuery) {
                throw new Exception("Gagal menyiapkan update data siswa: " . $conn->error);
            }
            $updateSiswaQuery->bind_param("iii", $kelas_id, $jurusan_id, $user_id);
            if (!$updateSiswaQuery->execute()) {
                throw new Exception("Gagal memperbarui data siswa: " . $updateSiswaQuery->error);
            }
            $updateSiswaQuery->close();
        } else {
            $insertSiswaQuery = $conn->prepare("INSERT INTO siswa (user_id, kelas_id, jurusan_id) VALUES (?, ?, ?)");
            if (!$insertSiswaQuery) {
                throw new Exception("Gagal menyiapkan insert data siswa: " . $conn->error);
            }
            $insertSiswaQuery->bind_param("iii", $user_id, $kelas_id, $jurusan_id);
            if (!$insertSiswaQuery->execute()) {
                throw new Exception("Gagal menambahkan data siswa: " . $insertSiswaQuery->error);
            }
            $insertSiswaQuery->close();
        }

        $_SESSION['kelas_id'] = $kelas_id;
        $_SESSION['jurusan_id'] = $jurusan_id;
    } elseif ($user_role === 'guru') {
        $jabatan_id = isset($_POST['jabatan_id']) ? intval($_POST['jabatan_id']) : null;

        if ($jabatan_id === 0 || $jabatan_id === null) {
            throw new Exception("Jabatan wajib dipilih untuk guru.");
        }

        $checkGuruQuery = $conn->prepare("SELECT id FROM guru WHERE user_id = ? LIMIT 1");
        $checkGuruQuery->bind_param("i", $user_id);
        $checkGuruQuery->execute();
        $resultCheckGuru = $checkGuruQuery->get_result();
        $guruExists = $resultCheckGuru->num_rows > 0;
        $checkGuruQuery->close();

        if ($guruExists) {
            $updateGuruQuery = $conn->prepare("UPDATE guru SET jabatan_id = ? WHERE user_id = ?");
            if (!$updateGuruQuery) {
                throw new Exception("Gagal menyiapkan update data guru: " . $conn->error);
            }
            $updateGuruQuery->bind_param("ii", $jabatan_id, $user_id);
            if (!$updateGuruQuery->execute()) {
                throw new Exception("Gagal memperbarui data guru: " . $updateGuruQuery->error);
            }
            $updateGuruQuery->close();
        } else {
            $insertGuruQuery = $conn->prepare("INSERT INTO guru (user_id, jabatan_id) VALUES (?, ?)");
            if (!$insertGuruQuery) {
                throw new Exception("Gagal menyiapkan insert data guru: " . $conn->error);
            }
            $insertGuruQuery->bind_param("ii", $user_id, $jabatan_id);
            if (!$insertGuruQuery->execute()) {
                throw new Exception("Gagal menambahkan data guru: " . $insertGuruQuery->error);
            }
            $insertGuruQuery->close();
        }
    }

    $_SESSION['success'] = "Profil berhasil diperbarui!";
} catch (Exception $e) {
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
} finally {
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
}

// Arahkan kembali ke halaman account-setting.php
header("Location: profile.php"); // Dipastikan redirect ke profile.php
exit();