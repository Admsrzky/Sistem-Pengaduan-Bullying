<?php
include './layout/header.php'; // Include header (pastikan tidak ada dark mode CSS di sini)
include 'config/database.php'; // Koneksi ke DB

// Pastikan user sudah login
if (!isset($_SESSION['nis_nip'])) {
    header('Location: login.php');
    exit();
}

// Inisialisasi variabel user, siswa, dan guru
$user = null;
$siswa = null; // Akan terisi jika role 'siswa'
$guru = null; // Akan terisi jika role 'guru'
$kelasList = [];
$jurusanList = [];
$jabatanList = []; // New: Akan terisi daftar jabatan untuk dropdown guru

// --- Ambil Data yang Diperlukan dari Database dengan Prepared Statements ---
try {
    // 1. Ambil data Kelas untuk dropdown
    $queryKelas = $conn->prepare("SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
    if ($queryKelas) {
        $queryKelas->execute();
        $resultKelas = $queryKelas->get_result();
        while ($k = $resultKelas->fetch_assoc()) {
            $kelasList[] = $k;
        }
        $queryKelas->close();
    } else {
        error_log("Error preparing kelas query: " . $conn->error);
    }

    // 2. Ambil data Jurusan untuk dropdown
    $queryJurusan = $conn->prepare("SELECT id, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC");
    if ($queryJurusan) {
        $queryJurusan->execute();
        $resultJurusan = $queryJurusan->get_result();
        while ($j = $resultJurusan->fetch_assoc()) {
            $jurusanList[] = $j;
        }
        $queryJurusan->close();
    } else {
        error_log("Error preparing jurusan query: " . $conn->error);
    }

    // 3. NEW: Ambil data Jabatan untuk dropdown (jika role guru)
    $queryJabatan = $conn->prepare("SELECT id, nama_jabatan FROM jabatan_guru ORDER BY nama_jabatan ASC");
    if ($queryJabatan) {
        $queryJabatan->execute();
        $resultJabatan = $queryJabatan->get_result();
        while ($jb = $resultJabatan->fetch_assoc()) {
            $jabatanList[] = $jb;
        }
        $queryJabatan->close();
    } else {
        error_log("Error preparing jabatan_guru query: " . $conn->error);
    }

    // 4. Ambil data user dari tabel 'users' berdasarkan nis_nip dari session
    // HAPUS KOLOM 'jabatan' dari SELECT users, karena akan diambil dari tabel guru
    $nis_nip_session = $_SESSION['nis_nip'];
    $queryUser = $conn->prepare("SELECT id, nis_nip, nama, foto_profile, role FROM users WHERE nis_nip = ? LIMIT 1");
    if (!$queryUser) {
        throw new Exception("Gagal menyiapkan query user: " . $conn->error);
    }
    $queryUser->bind_param("s", $nis_nip_session);
    $queryUser->execute();
    $resultUser = $queryUser->get_result();
    $user = $resultUser->fetch_assoc();
    $queryUser->close();

    if (!$user) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit();
    }

    // 5. Ambil data spesifik berdasarkan role
    $user_id = $user['id']; // Ambil user_id setelah data $user didapatkan

    if ($user['role'] === 'siswa') {
        $querySiswa = $conn->prepare("SELECT id, user_id, kelas_id, jurusan_id FROM siswa WHERE user_id = ? LIMIT 1");
        if (!$querySiswa) {
            throw new Exception("Gagal menyiapkan query siswa: " . $conn->error);
        }
        $querySiswa->bind_param("i", $user_id); // 'i' for integer
        $querySiswa->execute();
        $resultSiswa = $querySiswa->get_result();
        $siswa = $resultSiswa->fetch_assoc();
        $querySiswa->close();
    } elseif ($user['role'] === 'guru') {
        // NEW: Ambil data guru dari tabel 'guru'
        $queryGuru = $conn->prepare("SELECT id, user_id, jabatan_id FROM guru WHERE user_id = ? LIMIT 1");
        if (!$queryGuru) {
            throw new Exception("Gagal menyiapkan query guru: " . $conn->error);
        }
        $queryGuru->bind_param("i", $user_id);
        $queryGuru->execute();
        $resultGuru = $queryGuru->get_result();
        $guru = $resultGuru->fetch_assoc(); // Mendapatkan data guru
        $queryGuru->close();
    }
} catch (Exception $e) {
    $_SESSION['error'] = "Terjadi kesalahan saat memuat data: " . $e->getMessage();
} finally {
    if (isset($conn) && $conn->ping()) {
        $conn->close();
    }
}
?>

<div class="min-h-screen bg-gradient-to-br from-purple-50 to-pink-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto bg-white p-8 sm:p-10 rounded-xl shadow-2xl animate-fade-in-down">
        <h2 class="text-4xl font-extrabold mb-10 text-center text-pink-700 tracking-tight">Edit Profil</h2>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg font-medium flex items-center justify-between"
            role="alert">
            <span><i class="fas fa-exclamation-triangle mr-2"></i><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button type="button" class="text-red-700 hover:text-red-900 focus:outline-none"
                onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg font-medium flex items-center justify-between"
            role="alert">
            <span><i class="fas fa-check-circle mr-2"></i><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button type="button" class="text-green-700 hover:text-green-900 focus:outline-none"
                onclick="this.parentElement.style.display='none';">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="update-profile.php" method="POST" enctype="multipart/form-data" class="space-y-8">
            <div class="flex flex-col md:flex-row items-center md:items-start md:space-x-10">
                <div class="text-center md:w-1/3 flex flex-col items-center">
                    <div
                        class="w-40 h-40 mx-auto rounded-full overflow-hidden border-4 border-pink-500 shadow-xl ring-2 ring-pink-300 ring-offset-2">
                        <img src="assets/img/profile/<?= htmlspecialchars($user['foto_profile'] ?? 'default.png') ?>"
                            alt="Foto Profil"
                            class="object-cover w-full h-full transition-transform duration-300 hover:scale-105">
                    </div>
                    <label class="block mt-6 text-base font-semibold text-gray-700">Ubah Foto Profil</label>
                    <input type="file" name="foto" accept="image/*"
                        class="mt-3 w-full text-sm text-gray-600
                        file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold
                        file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 transition duration-200 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Maks. 2MB. Format: JPG, PNG, GIF.</p>
                </div>

                <div class="mt-10 md:mt-0 md:w-2/3 space-y-6">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-1">NIS/NIP</label>
                        <input type="text" value="<?= htmlspecialchars($user['nis_nip'] ?? '') ?>" readonly
                            class="w-full px-4 py-2 border border-gray-300 rounded-md bg-gray-100 text-gray-800 cursor-not-allowed shadow-sm">
                    </div>

                    <div>
                        <label for="nama" class="block text-gray-700 font-semibold mb-1">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user['nama'] ?? '') ?>"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 shadow-sm transition duration-150 text-gray-900">
                    </div>

                    <?php if ($user['role'] === 'siswa'): ?>
                    <div>
                        <label for="kelas_id" class="block text-gray-700 font-semibold mb-1">Kelas</label>
                        <select id="kelas_id" name="kelas_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 shadow-sm transition duration-150 text-gray-900">
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($kelasList as $k): ?>
                            <option value="<?= htmlspecialchars($k['id']) ?>"
                                <?= (isset($siswa['kelas_id']) && $siswa['kelas_id'] == $k['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($k['nama_kelas']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label for="jurusan_id" class="block text-gray-700 font-semibold mb-1">Jurusan</label>
                        <select id="jurusan_id" name="jurusan_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 shadow-sm transition duration-150 text-gray-900">
                            <option value="">Pilih Jurusan</option>
                            <?php foreach ($jurusanList as $j): ?>
                            <option value="<?= htmlspecialchars($j['id']) ?>"
                                <?= (isset($siswa['jurusan_id']) && $siswa['jurusan_id'] == $j['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($j['nama_jurusan']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php elseif ($user['role'] === 'guru'): ?>
                    <div>
                        <label for="jabatan_id" class="block text-gray-700 font-semibold mb-1">Jabatan</label>
                        <select id="jabatan_id" name="jabatan_id"
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 shadow-sm transition duration-150 text-gray-900">
                            <option value="">Pilih Jabatan</option>
                            <?php foreach ($jabatanList as $jb): ?>
                            <option value="<?= htmlspecialchars($jb['id']) ?>"
                                <?= (isset($guru['jabatan_id']) && $guru['jabatan_id'] == $jb['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($jb['nama_jabatan']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Pilih jabatan Anda (contoh: Guru Matematika, Wali Kelas X
                            RPL).</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="text-center pt-8">
                <button type="submit"
                    class="bg-pink-600 text-white px-10 py-3 rounded-full font-bold text-lg hover:bg-pink-700 shadow-lg transform hover:scale-105 transition duration-300 ease-in-out focus:outline-none focus:ring-4 focus:ring-pink-300">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>