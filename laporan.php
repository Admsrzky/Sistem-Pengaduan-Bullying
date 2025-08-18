<?php
ob_start(); // TAMBAHKAN BARIS INI SEBAGAI BARIS PERTAMA DI FILE INI

include 'layout/header.php';
include 'config/database.php'; // Pastikan file ini menghubungkan ke database dan membuat variabel $conn
include 'auth/auth.php'; // Pastikan ini berisi fungsi cek_login() dan cek_role()

// --- PEMERIKSAAN LOGIN SANGAT PENTING DI SINI ---
// Jika user belum login (nis_nip tidak ada di sesi), arahkan ke halaman login
if (!isset($_SESSION['nis_nip']) || empty($_SESSION['nis_nip']) || !isset($_SESSION['id'])) {
    header('Location: login.php'); // Arahkan ke halaman login
    exit(); // Hentikan eksekusi skrip agar tidak ada kode lain yang dijalankan
}
// ----------------------------------------------------

// Ambil daftar kategori
$kategoriList = [];
// Periksa apakah koneksi database ada dan berfungsi
if (isset($conn) && $conn->ping()) {
    $kategoriQuery = "SELECT id, nama_kategori FROM kategori_laporan ORDER BY nama_kategori ASC";
    $kategoriResult = $conn->query($kategoriQuery);
    if ($kategoriResult && $kategoriResult->num_rows > 0) {
        while ($row = $kategoriResult->fetch_assoc()) {
            $kategoriList[] = $row;
        }
        $kategoriResult->free(); // Bebaskan hasil query dari memori
    }
} else {
    // Catat jika ada masalah koneksi database saat mengambil kategori
    error_log("Koneksi database gagal saat mengambil kategori!");
    // Anda bisa menambahkan log atau tindakan lain di sini,
    // misalnya menampilkan pesan error kepada pengguna atau menghentikan skrip.
}

// Definisikan path dasar untuk file bukti di web (digunakan oleh JavaScript)
$asset_base_path_bukti_web = 'uploads/'; // Relatif terhadap halaman laporan.php


// --- Bagian Utama: Memproses Pengiriman Formulir ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Verifikasi Login Pengguna
        if (!isset($_SESSION['nis_nip']) || empty($_SESSION['nis_nip'])) {
            throw new Exception('Anda belum login. Silakan login terlebih dahulu.');
        }

        // 2. Ambil ID Pengguna dari Database (lebih aman dengan parameterized query)
        $queryUser = $conn->prepare("SELECT id FROM users WHERE nis_nip = ? LIMIT 1");
        if (!$queryUser) {
            throw new Exception('Kesalahan sistem saat menyiapkan data pengguna: ' . $conn->error);
        }
        $queryUser->bind_param("s", $_SESSION['nis_nip']); // 's' untuk string
        $queryUser->execute();
        $hasilUser = $queryUser->get_result();

        if ($hasilUser->num_rows === 0) {
            throw new Exception('Data pengguna tidak ditemukan di database!');
        }

        $dataUser = $hasilUser->fetch_assoc();
        $user_id = $dataUser['id'];
        $queryUser->close(); // Tutup query untuk pengguna

        // 3. Ambil dan Bersihkan Input dari Formulir
        $kategori_id        = isset($_POST['kategori']) ? intval($_POST['kategori']) : 0;
        $kronologi          = isset($_POST['kronologi']) ? trim($_POST['kronologi']) : '';
        $lokasi             = isset($_POST['lokasi']) ? trim($_POST['lokasi']) : '';
        $tanggal_kejadian   = isset($_POST['tanggal_kejadian']) ? $_POST['tanggal_kejadian'] : '';

        // 4. Validasi Input Dasar
        if ($kategori_id === 0 || empty($kronologi) || empty($lokasi) || empty($tanggal_kejadian)) {
            throw new Exception('Semua bidang yang bertanda (*) wajib diisi!');
        }

        // Validasi format tanggal (misal: YYYY-MM-DD)
        if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $tanggal_kejadian)) {
            throw new Exception('Format tanggal kejadian tidak valid. Gunakan format YYYY-MM-DD.');
        }

        // 5. Penanganan dan Validasi File Bukti
        $nama_bukti = null; // Variabel untuk menyimpan nama file yang diunggah

        // Periksa apakah file bukti telah diunggah
        if (!isset($_FILES['bukti']) || $_FILES['bukti']['error'] === UPLOAD_ERR_NO_FILE) {
            throw new Exception('Anda wajib mengunggah file bukti!');
        }

        // Jika tidak ada error saat mengunggah file dari sisi PHP
        if ($_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
            // Tentukan tipe MIME yang diizinkan
            $allowed_mimes = [
                'image/jpeg',
                'image/png',
                'image/gif',
                'image/webp',
                'application/pdf',
                'video/mp4',
                'video/webm',
                'video/ogg',
                'video/quicktime',
                'video/x-msvideo'
            ];

            $file_tmp = $_FILES['bukti']['tmp_name'];
            $file_size = $_FILES['bukti']['size'];
            $max_file_size = 500 * 1024 * 1024; // Batas ukuran 50 MB

            // Validasi ukuran file terlebih dahulu (lebih cepat jika file terlalu besar)
            if ($file_size > $max_file_size) {
                throw new Exception('Ukuran file melebihi batas maksimal ' . ($max_file_size / (1024 * 1024)) . 'MB. Silakan unggah file yang lebih kecil.');
            }

            // Dapatkan tipe MIME aktual dari file menggunakan fileinfo (lebih aman)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $actual_mime_type = ($finfo) ? finfo_file($finfo, $file_tmp) : $_FILES['bukti']['type'];
            if ($finfo) finfo_close($finfo);

            // Dapatkan ekstensi file dari nama aslinya
            $ext = pathinfo($_FILES['bukti']['name'], PATHINFO_EXTENSION);
            $is_allowed_mime = in_array($actual_mime_type, $allowed_mimes);
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'mp4', 'webm', 'ogg', 'mov', 'avi', 'flv', '3gp', 'wmv'];
            $is_allowed_extension = in_array(strtolower($ext), $allowed_extensions);

            // Periksa apakah tipe MIME atau ekstensi tidak diizinkan
            // Menggunakan operator OR (||) berarti jika SALAH SATU tidak cocok, maka akan ditolak.
            if (!$is_allowed_mime || !$is_allowed_extension) {
                throw new Exception('Format file tidak didukung (MIME: ' . htmlspecialchars($actual_mime_type) . ', Ekstensi: ' . htmlspecialchars($ext) . '). Harap unggah gambar, video, atau PDF yang valid.');
            }

            // Buat nama file unik untuk mencegah konflik dan masalah keamanan
            $nama_baru_file = uniqid("bukti_") . '.' . $ext;
            $upload_dir = 'uploads/'; // Folder tempat file akan disimpan

            // Pastikan folder uploads ada, jika belum, buat
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0777, true)) { // 0777 untuk izin penuh, sesuaikan di server produksi
                    throw new Exception('Gagal membuat folder untuk unggahan file.');
                }
            }
            $jalur_unggah_lengkap = $upload_dir . $nama_baru_file;

            // Pindahkan file dari lokasi sementara ke lokasi permanen
            if (move_uploaded_file($file_tmp, $jalur_unggah_lengkap)) {
                $nama_bukti = $nama_baru_file; // Simpan nama file yang sudah diunggah
            } else {
                // Tangani error jika file gagal dipindahkan
                $kode_error_unggah = $_FILES['bukti']['error'];
                $pesan_error_unggah = [
                    UPLOAD_ERR_INI_SIZE => "File melebihi batas ukuran yang diizinkan server (php.ini).",
                    UPLOAD_ERR_FORM_SIZE => "File melebihi batas ukuran yang ditentukan formulir.",
                    UPLOAD_ERR_PARTIAL => "File hanya terunggah sebagian.",
                    UPLOAD_ERR_NO_TMP_DIR => "Direktori sementara untuk unggahan tidak ditemukan.",
                    UPLOAD_ERR_CANT_WRITE => "Gagal menyimpan file ke disk.",
                    UPLOAD_ERR_EXTENSION => "Ekstensi PHP menghentikan unggahan file."
                ];
                $deskripsi_error = $pesan_error_unggah[$kode_error_unggah] ?? "Kode error tidak dikenal: " . $kode_error_unggah;
                throw new Exception('Gagal mengunggah file bukti. ' . $deskripsi_error);
            }
        } else {
            // Tangani error umum saat mengunggah file jika bukan karena tidak ada file
            $kode_error_unggah = $_FILES['bukti']['error'];
            if ($kode_error_unggah === UPLOAD_ERR_NO_FILE) {
                throw new Exception('Anda wajib mengunggah file bukti!');
            }
            $pesan_error_unggah = [
                UPLOAD_ERR_INI_SIZE => "File melebihi batas ukuran yang diizinkan server (php.ini).",
                UPLOAD_ERR_FORM_SIZE => "File melebihi batas ukuran yang ditentukan formulir.",
                UPLOAD_ERR_PARTIAL => "File hanya terunggah sebagian.",
                UPLOAD_ERR_NO_TMP_DIR => "Direktori sementara untuk unggahan tidak ditemukan.",
                UPLOAD_ERR_CANT_WRITE => "Gagal menyimpan file ke disk.",
                UPLOAD_ERR_EXTENSION => "Ekstensi PHP menghentikan unggahan file."
            ];
            $deskripsi_error = $pesan_error_unggah[$kode_error_unggah] ?? "Kode error tidak dikenal: " . $kode_error_unggah;
            throw new Exception('Terjadi kesalahan saat mengunggah file: ' . $deskripsi_error);
        }

        // 6. Siapkan Data untuk Disimpan ke Database
        $status_laporan = 'terkirim';
        $waktu_dibuat = date('Y-m-d H:i:s');
        $waktu_diupdate = $waktu_dibuat;

        // 7. Simpan Laporan ke Database (lebih aman dengan parameterized query)
        $queryInsertLaporan = "INSERT INTO laporan
            (user_id, kategori_id, kronologi, lokasi, tanggal_kejadian, bukti, status, created_at, updated_at)
            VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $persiapanQuery = $conn->prepare($queryInsertLaporan);
        if (!$persiapanQuery) {
            throw new Exception('Kesalahan sistem saat menyiapkan penyimpanan laporan: ' . $conn->error);
        }

        // Ikat nilai ke placeholder (?) pada query
        $persiapanQuery->bind_param("iisssssss", $user_id, $kategori_id, $kronologi, $lokasi, $tanggal_kejadian, $nama_bukti, $status_laporan, $waktu_dibuat, $waktu_diupdate);
        // 'i' untuk integer, 's' untuk string

        if ($persiapanQuery->execute()) {
            $_SESSION['success_message'] = 'Laporan Anda berhasil dikirimkan!';
            $persiapanQuery->close(); // Tutup query laporan sebelum exit
            header('Location: lapor-sukses.php'); // Arahkan ke halaman sukses
            exit;
        } else {
            // Jika ada masalah saat menyimpan ke database, hapus file yang sudah diunggah
            if ($nama_bukti && file_exists($upload_dir . $nama_bukti)) {
                unlink($upload_dir . $nama_bukti); // Hapus file dari server
                error_log("File " . $nama_bukti . " dihapus karena kegagalan menyimpan ke database.");
            }
            $persiapanQuery->close(); // Tutup query laporan sebelum throw
            throw new Exception('Gagal menyimpan laporan ke database: ' . $persiapanQuery->error);
        }
    } catch (Exception $e) {
        // Tangkap semua kesalahan dan tampilkan pesan error
        $_SESSION['error_message'] = $e->getMessage();
        // Arahkan kembali ke halaman sebelumnya atau ke halaman laporan
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'laporan.php'));
        exit;
    } finally {
        // Pastikan koneksi database ditutup di akhir, jika masih aktif
        if (isset($conn) && $conn->ping()) {
            $conn->close();
        }
    }
}
?>

<div
    class="flex justify-center items-center min-h-screen bg-gradient-to-br from-blue-50 to-purple-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-xl shadow-2xl overflow-hidden max-w-4xl w-full p-8 space-y-8 animate-fade-in">
        <h1 class="text-4xl font-extrabold text-gray-900 text-center tracking-tight mb-6">Form Laporan Bullying dan
            pelecehan seksual</h1>

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 font-medium"
                role="alert">
                <strong class="font-bold">Error!</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['error_message']) ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer"
                    onclick="this.parentElement.style.display='none';">
                    <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-2.652a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.652 2.651a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative mb-4 font-medium"
                role="alert">
                <strong class="font-bold">Sukses!</strong>
                <span class="block sm:inline"><?= htmlspecialchars($_SESSION['success_message']) ?></span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer"
                    onclick="this.parentElement.style.display='none';">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20">
                        <title>Close</title>
                        <path
                            d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.697l-2.651 2.652a1.2 1.2 0 1 1-1.697-1.697L8.303 10 5.651 7.348a1.2 1.2 0 1 1 1.697-1.697L10 8.303l2.651-2.652a1.2 1.2 0 1 1 1.697 1.697L11.697 10l2.652 2.651a1.2 1.2 0 0 1 0 1.698z" />
                    </svg>
                </span>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data"
            class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

            <div class="space-y-6">
                <div>
                    <label for="nis_nip" class="block text-sm font-semibold text-gray-700 mb-1">NIS/NIP</label>
                    <input type="text" id="nis_nip" name="nis_nip"
                        value="<?= htmlspecialchars($_SESSION['nis_nip'] ?? '') ?>" readonly
                        class="form-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-800 cursor-not-allowed text-base" />
                </div>

                <div>
                    <label for="nama_pelapor" class="block text-sm font-semibold text-gray-700 mb-1">Nama
                        Pelapor</label>
                    <input type="text" id="nama_pelapor" name="nama_pelapor"
                        value="<?= htmlspecialchars($_SESSION['nama'] ?? '') ?>" readonly
                        class="form-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 text-gray-800 cursor-not-allowed text-base" />
                </div>

                <div>
                    <label for="kategori" class="block text-sm font-semibold text-gray-700 mb-1">Kategori <span
                            class="text-red-500">*</span></label>
                    <select id="kategori" name="kategori" required
                        class="form-select block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base text-gray-800">
                        <option value="" disabled selected class="text-gray-600">Pilih kategori bullying</option>
                        <?php foreach ($kategoriList as $kategori): ?>
                            <option value="<?= htmlspecialchars($kategori['id']) ?>">
                                <?= htmlspecialchars($kategori['nama_kategori']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="tanggal_kejadian" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal
                        Kejadian <span class="text-red-500">*</span></label>
                    <input type="date" id="tanggal_kejadian" name="tanggal_kejadian" required
                        class="form-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base text-gray-800" />
                </div>
            </div>

            <div class="space-y-6">
                <div>
                    <label for="lokasi" class="block text-sm font-semibold text-gray-700 mb-1">Lokasi Kejadian <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="lokasi" name="lokasi" required
                        class="form-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base placeholder-gray-600 text-gray-800"
                        placeholder="Mencoba mendeteksi lokasi otomatis..." readonly />
                    <p id="lokasi-status" class="text-xs text-gray-700 mt-1 italic">
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Peta Lokasi</label>
                    <div id="map"
                        class="w-full h-48 md:h-64 lg:h-80 xl:h-96 rounded-md border border-gray-300 shadow-inner bg-gray-50 flex items-center justify-center text-gray-700">
                        Memuat peta...
                    </div>
                </div>

                <div>
                    <label for="bukti" class="block text-sm font-semibold text-gray-700 mb-1">Bukti (wajib) <span
                            class="text-red-500">*</span></label>
                    <input type="file" id="bukti" name="bukti" accept="image/*,video/*,application/pdf" required
                        class="form-input block w-full text-gray-700 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base text-gray-800" />
                    <p class="text-xs text-red-600 mt-1 italic">Bukti harus berupa gambar, video (MP4, WebM, Ogg, MOV,
                        AVI), atau PDF, maksimal 50MB.</p>
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="kronologi" class="block text-sm font-semibold text-gray-700 mb-1">Kronologi <span
                        class="text-red-500">*</span></label>
                <textarea id="kronologi" name="kronologi" rows="5" required
                    class="form-textarea block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-base placeholder-gray-600 text-gray-800"
                    placeholder="Jelaskan kronologi kejadian bullying secara detail"></textarea>
            </div>

            <div class="md:col-span-2 text-center">
                <button type="submit"
                    class="w-full sm:w-auto px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Kirim Laporan
                </button>
            </div>

        </form>
    </div>
</div>

<script src="assets/js/map.js"></script>
<?php include 'layout/footer.php'; ?>