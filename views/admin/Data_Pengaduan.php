<?php
// Include the header, which handles session_start(), authentication,
// and sets up $user_id, $user_nama, $user_foto_profile, and database connection.
include 'header.php';

// Initialize variables
$success_message = '';
$error_message = '';

// Check for messages passed via session
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Ensure database connection is available
if (!isset($conn) || $conn->connect_error) {
    $error_message = "Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error');
}

$asset_base_path_bukti_web = '../../uploads/';

$asset_base_path_bukti_server = realpath(__DIR__ . '/../../uploads/') . DIRECTORY_SEPARATOR;

include '../../controllers/PengaduanController.php';


?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
        Manajemen Data Pengaduan / Laporan
    </h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($success_message) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($error_message) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daftar Laporan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            No.
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Pelapor (NIS/NIP)
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Kategori
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Kronologi
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Lokasi
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Tgl. Kejadian
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Bukti
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Status
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Dibuat Pada
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Terakhir Diubah
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporan_data)): ?>
                        <tr>
                            <td colspan="11"
                                class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200 text-center">
                                Tidak ada data laporan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($laporan_data as $laporan): ?>
                            <tr>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= $no++; ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['nama_pelapor'] ?? 'N/A') ?>
                                    <br>
                                    <span class="text-xs text-gray-500">
                                        (<?= htmlspecialchars($laporan['nisnip_pelapor'] ?? 'N/A') ?>)
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['nama_kategori'] ?? 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars(mb_strimwidth($laporan['kronologi'] ?? '', 0, 70, "...")) ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['lokasi'] ?? 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['tanggal_kejadian'] ? date('d M Y', strtotime($laporan['tanggal_kejadian'])) : 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?php if (!empty($laporan['bukti'])): ?>
                                        <?php
                                        $bukti_web_path = $asset_base_path_bukti_web . htmlspecialchars($laporan['bukti']);
                                        $bukti_server_actual_path = $asset_base_path_bukti_server . htmlspecialchars($laporan['bukti']);

                                        $file_exists = file_exists($bukti_server_actual_path);
                                        $is_image = false;
                                        $is_video = false;
                                        $is_pdf = false;

                                        if ($file_exists) {
                                            $mime_type = mime_content_type($bukti_server_actual_path);
                                            if ($mime_type === false) { // Fallback if mime_content_type fails
                                                $file_extension = pathinfo($laporan['bukti'], PATHINFO_EXTENSION);
                                                $is_image = in_array(strtolower($file_extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $is_video = in_array(strtolower($file_extension), ['mp4', 'webm', 'ogg', 'mov', 'avi', 'flv', '3gp', 'wmv']);
                                                $is_pdf = strtolower($file_extension) === 'pdf';
                                            } else {
                                                $is_image = str_starts_with($mime_type, 'image/');
                                                $is_video = str_starts_with($mime_type, 'video/');
                                                $is_pdf = $mime_type === 'application/pdf';
                                            }
                                        }
                                        ?>
                                        <?php if ($file_exists): ?>
                                            <a href="<?= $bukti_web_path ?>" class="text-blue-500 hover:underline"
                                                <?php if ($is_image): ?> data-lightbox="laporan-<?= $laporan['id'] ?>"
                                                data-title="Bukti Laporan #<?= $laporan['id'] ?>" <?php endif; ?>>
                                                <?php if ($is_image): ?>
                                                    <img src="<?= $bukti_web_path ?>" alt="Bukti" class="w-16 h-16 object-cover rounded">
                                                <?php elseif ($is_video): ?>
                                                    <video src="<?= $bukti_web_path ?>" controls class="w-20 h-16 object-cover rounded"
                                                        preload="metadata"></video>
                                                <?php elseif ($is_pdf): ?>
                                                    Lihat PDF
                                                <?php else: ?>
                                                    Lihat File
                                                <?php endif; ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-red-500">File tidak ditemukan</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        N/A
                                    <?php endif; ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                <?php
                                switch ($laporan['status']) {
                                    case 'terkirim':
                                        echo 'bg-blue-100 text-blue-800';
                                        break;
                                    case 'diproses':
                                        echo 'bg-yellow-100 text-yellow-800';
                                        break;
                                    case 'selesai':
                                        echo 'bg-green-100 text-green-800';
                                        break;
                                    case 'ditolak':
                                        echo 'bg-red-100 text-red-800';
                                        break;
                                    default:
                                        echo 'bg-gray-100 text-gray-800';
                                        break;
                                }
                                ?>">
                                        <?= htmlspecialchars(ucfirst($laporan['status'] ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['created_at'] ? date('d M Y H:i', strtotime($laporan['created_at'])) : 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['updated_at'] ? date('d M Y H:i', strtotime($laporan['updated_at'])) : 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <?php
                                        // Encode sanksi data for this report to pass to JS
                                        $current_laporan_sanksi = $sanksi_by_laporan_id[$laporan['id']] ?? [];
                                        $sanksi_json = htmlspecialchars(json_encode($current_laporan_sanksi), ENT_QUOTES, 'UTF-8');
                                        ?>
                                        <button
                                            class="text-teal-600 hover:text-teal-900 dark:text-teal-400 dark:hover:text-teal-200 view-sanksi-btn"
                                            data-laporan-id="<?= htmlspecialchars($laporan['id']) ?>"
                                            data-sanksi-data='<?= $sanksi_json ?>' title="Lihat Sanksi">
                                            <i data-feather="eye" class="w-5 h-5"></i>
                                        </button>

                                        <button
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 edit-btn"
                                            data-id="<?= htmlspecialchars($laporan['id'] ?? '') ?>"
                                            data-kategori-id="<?= htmlspecialchars($laporan['kategori_id'] ?? '') ?>"
                                            data-kronologi="<?= htmlspecialchars($laporan['kronologi'] ?? '') ?>"
                                            data-lokasi="<?= htmlspecialchars($laporan['lokasi'] ?? '') ?>"
                                            data-tanggal-kejadian="<?= htmlspecialchars($laporan['tanggal_kejadian'] ? date('Y-m-d', strtotime($laporan['tanggal_kejadian'])) : '') ?>"
                                            data-bukti="<?= htmlspecialchars($laporan['bukti'] ?? '') ?>"
                                            data-status="<?= htmlspecialchars($laporan['status'] ?? '') ?>">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>
                                        <form action="" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini? Tindakan ini tidak dapat dibatalkan.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="delete_laporan_id"
                                                value="<?= htmlspecialchars($laporan['id'] ?? '') ?>">
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
                                                <i data-feather="trash-2" class="w-5 h-5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="editLaporanModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit Laporan</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_laporan_id" name="edit_laporan_id">
                <input type="hidden" id="current_kategori_id_display" name="current_kategori_id_display">
                <input type="hidden" id="current_kronologi_display" name="current_kronologi_display">
                <input type="hidden" id="current_lokasi_display" name="current_lokasi_display">
                <input type="hidden" id="current_tanggal_kejadian_display" name="current_tanggal_kejadian_display">
                <input type="hidden" id="current_bukti_file" name="current_bukti_file">
                <input type="hidden" id="current_status_display" name="current_status_display">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="edit_kategori_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Kategori Laporan
                        </label>
                        <select id="edit_kategori_id" name="edit_kategori_id"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            required>
                            <?php foreach ($kategori_options as $kategori): ?>
                                <option value="<?= htmlspecialchars($kategori['id']) ?>"
                                    <?= (isset($laporan['kategori_id']) && $laporan['kategori_id'] == $kategori['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kategori['nama_kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="edit_tanggal_kejadian"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Tanggal Kejadian
                        </label>
                        <input type="date" id="edit_tanggal_kejadian" name="edit_tanggal_kejadian"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            required>
                    </div>
                    <div class="mb-4 col-span-full">
                        <label for="edit_kronologi"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Kronologi / Deskripsi Kejadian
                        </label>
                        <textarea id="edit_kronologi" name="edit_kronologi" rows="4"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            required></textarea>
                    </div>
                    <div class="mb-4 col-span-full">
                        <label for="edit_lokasi" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Lokasi Kejadian
                        </label>
                        <input type="text" id="edit_lokasi" name="edit_lokasi"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            required>
                    </div>
                    <div class="mb-4 col-span-full">
                        <label for="edit_status" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Status Laporan
                        </label>
                        <select id="edit_status" name="edit_status"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                            required>
                            <?php foreach ($status_options as $status_opt): ?>
                                <option value="<?= htmlspecialchars($status_opt) ?>">
                                    <?= htmlspecialchars(ucfirst($status_opt)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4 col-span-full">
                        <label for="edit_bukti_file"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Ganti File Bukti (Opsional)
                        </label>
                        <input type="file" id="edit_bukti_file" name="edit_bukti_file"
                            accept="image/*,video/*,application/pdf"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ukuran maks: 50MB. Format: Gambar (JPG,
                            JPEG, PNG, GIF), Video (MP4, WebM, Ogg, MOV, AVI), atau PDF.</p>
                        <div class="mt-2" id="current_bukti_preview_container">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Bukti saat ini:</span>
                            <span id="current_bukti_display" class="ml-2 text-blue-500 hover:underline"></span>
                            <label class="ml-4 inline-flex items-center text-sm text-red-600 cursor-pointer">
                                <input type="checkbox" name="clear_bukti_file" value="1"
                                    class="form-checkbox h-4 w-4 text-red-600" id="clear_bukti_file_checkbox">
                                <span class="ml-1">Hapus Bukti Lama</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeEditModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-white">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="viewSanksiModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Detail Sanksi Laporan <span
                    id="sanksi_laporan_id_display"></span></h3>
            <div id="sanksi_details_content"
                class="max-h-96 overflow-y-auto mb-4 p-2 border rounded dark:border-gray-700">
            </div>
            <div class="flex justify-end">
                <button type="button" id="closeViewSanksiModal"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-white">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</main>

<scrip src="https://cdn.jsdelivr.net/npm/simple-lightbox@2.14.2/dist/simple-lightbox.min.js">
    </script>
    <script src="../../assets/js/DataPengaduan.js"></script>


    <?php include 'footer.php'; ?>