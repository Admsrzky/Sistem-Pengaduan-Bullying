<?php
// Include the header, which handles session_start(), authentication,
// and sets up $user_id, $user_nama, $user_foto_profile, and database connection.
include 'header.php';

// Set default timezone for consistent date handling
date_default_timezone_set('Asia/Jakarta');

// Initialize variables
$success_message = '';
$error_message = '';
$notification_message = ''; // New variable to consolidate messages

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

include '../../controllers/LaporanController.php';
?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
        Rekapitulasi Data Laporan Pengaduan
    </h2>

    <?php if (!empty($notification_message)): ?>
        <?php
        $bg_class = '';
        $text_class = '';
        $border_class = '';
        $strong_text = '';

        switch ($notification_type) {
            case 'success':
                $bg_class = 'bg-green-100';
                $text_class = 'text-green-700';
                $border_class = 'border-green-400';
                $strong_text = 'Sukses!';
                break;
            case 'error':
                $bg_class = 'bg-red-100';
                $text_class = 'text-red-700';
                $border_class = 'border-red-400';
                $strong_text = 'Error!';
                break;
            case 'info':
            default: // Default to info if type is not recognized
                $bg_class = 'bg-blue-100';
                $text_class = 'text-blue-700';
                $border_class = 'border-blue-400';
                $strong_text = 'Perhatian!'; // Or 'Info!', 'Pemberitahuan!'
                break;
        }
        ?>
        <div class="<?= $bg_class ?> <?= $border_class ?> <?= $text_class ?> px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold"><?= $strong_text ?></strong>
            <span class="block sm:inline"><?= htmlspecialchars($notification_message) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Filter Laporan</h3>
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="filter_day"
                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Hari</label>
                <select id="filter_day" name="filter_day"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="">Semua Hari</option>
                    <?php for ($i = 1; $i <= 31; $i++): ?>
                        <option value="<?= $i ?>" <?= ($filter_day == $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="filter_month"
                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Bulan</label>
                <select id="filter_month" name="filter_month"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="">Semua Bulan</option>
                    <?php
                    $months = [
                        1 => 'Januari',
                        2 => 'Februari',
                        3 => 'Maret',
                        4 => 'April',
                        5 => 'Mei',
                        6 => 'Juni',
                        7 => 'Juli',
                        8 => 'Agustus',
                        9 => 'September',
                        10 => 'Oktober',
                        11 => 'November',
                        12 => 'Desember'
                    ];
                    foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>" <?= ($filter_month == $num) ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="filter_year"
                    class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">Tahun</label>
                <select id="filter_year" name="filter_year"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="">Pilih Tahun</option> <?php
                                                            $current_year = date('Y');
                                                            for ($i = $current_year; $i >= $current_year - 5; $i--): // Filter 5 tahun terakhir
                                                            ?>
                        <option value="<?= $i ?>" <?= ($filter_year == $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="flex space-x-2">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Filter
                </button>
                <a href="<?= $_SERVER['PHP_SELF'] ?>"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-white">
                    Reset
                </a>
            </div>
        </form>
        <div class="mt-4 flex space-x-2">
            <?php if ($is_filter_set && !empty($laporan_data)): // Only show export buttons if data is displayed
            ?>
                <a href="export_excel.php?<?= http_build_query($_GET) ?>"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                    <i data-feather="file-text" class="w-5 h-5 mr-2"></i> Export Excel
                </a>
                <a href="export_pdf.php?<?= http_build_query($_GET) ?>"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline flex items-center">
                    <i data-feather="file" class="w-5 h-5 mr-2"></i> Export PDF
                </a>
            <?php elseif ($is_filter_set && empty($laporan_data) && empty($error_message)): ?>
                <p class="text-gray-600 dark:text-gray-400">Tidak ada data untuk diekspor dengan filter saat ini.</p>
            <?php else: ?>
                <p class="text-gray-600 dark:text-gray-400">Pilih tahun pada filter untuk mengaktifkan ekspor.</p>
            <?php endif; ?>
        </div>
    </div>

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
                            Sanksi Terkait
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Dibuat Pada
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-300 uppercase tracking-wider">
                            Terakhir Diubah
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$is_filter_set): // Show message if filter not set
                    ?>
                        <tr>
                            <td colspan="12"
                                class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200 text-center">
                                <p>Silakan pilih minimal tahun pada filter di atas untuk menampilkan data laporan.</p>
                            </td>
                        </tr>
                    <?php elseif (empty($laporan_data)): // Show if filter set but no data found
                    ?>
                        <tr>
                            <td colspan="12"
                                class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200 text-center">
                                Tidak ada data laporan ditemukan dengan filter saat ini.
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
                                        $mime_type = ''; // Default empty

                                        if ($file_exists) {
                                            // Attempt to get MIME type (requires fileinfo extension)
                                            if (function_exists('mime_content_type')) {
                                                $mime_type = mime_content_type($bukti_server_actual_path);
                                            }

                                            // Fallback to extension if mime_content_type fails or not available
                                            if ($mime_type === false || empty($mime_type)) {
                                                $file_extension = strtolower(pathinfo($laporan['bukti'], PATHINFO_EXTENSION));
                                                if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                                                    $mime_type = 'image/' . $file_extension;
                                                } elseif (in_array($file_extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'flv', '3gp', 'wmv'])) {
                                                    $mime_type = 'video/' . $file_extension;
                                                } elseif ($file_extension === 'pdf') {
                                                    $mime_type = 'application/pdf';
                                                }
                                            }
                                        }

                                        $is_image = str_starts_with($mime_type, 'image/');
                                        $is_video = str_starts_with($mime_type, 'video/');
                                        $is_pdf = $mime_type === 'application/pdf';
                                        ?>
                                        <?php if ($file_exists): ?>
                                            <?php if ($is_image): ?>
                                                <a href="<?= $bukti_web_path ?>" class="text-blue-500 hover:underline"
                                                    data-lightbox="laporan-<?= $laporan['id'] ?>"
                                                    data-title="Bukti Laporan #<?= $laporan['id'] ?>">
                                                    <img src="<?= $bukti_web_path ?>" alt="Bukti" class="w-16 h-16 object-cover rounded">
                                                </a>
                                            <?php elseif ($is_video): ?>
                                                <button class="text-blue-500 hover:underline view-video-btn"
                                                    data-video-src="<?= $bukti_web_path ?>"
                                                    data-title="Video Bukti Laporan #<?= $laporan['id'] ?>">
                                                    <video src="<?= $bukti_web_path ?>" controls class="w-20 h-16 object-cover rounded"
                                                        preload="metadata"></video>
                                                    <span class="block text-xs mt-1">Lihat Video</span>
                                                </button>
                                            <?php elseif ($is_pdf): ?>
                                                <a href="<?= $bukti_web_path ?>" target="_blank" class="text-blue-500 hover:underline">
                                                    Lihat PDF
                                                </a>
                                            <?php else: ?>
                                                <a href="<?= $bukti_web_path ?>" target="_blank" class="text-blue-500 hover:underline">
                                                    Lihat File
                                                </a>
                                            <?php endif; ?>
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
                                    <?php
                                    $current_laporan_id = $laporan['id'];
                                    if (isset($sanksi_by_laporan_id[$current_laporan_id]) && !empty($sanksi_by_laporan_id[$current_laporan_id])) {
                                        echo '<ul class="list-disc list-inside text-xs space-y-1">';
                                        foreach ($sanksi_by_laporan_id[$current_laporan_id] as $sanksi_item) {
                                            echo '<li>';
                                            echo '<strong>' . htmlspecialchars($sanksi_item['jenis_sanksi']) . '</strong>: ';
                                            echo htmlspecialchars(mb_strimwidth($sanksi_item['deskripsi'], 0, width: 400,));
                                            echo ' <span class="text-gray-500">(' . htmlspecialchars(date('d/m/Y', strtotime($sanksi_item['tanggal_mulai']))) . ')</span>';
                                            echo '</li>';
                                        }
                                        echo '</ul>';
                                        // Tombol untuk melihat detail sanksi di modal jika ada lebih dari 1 atau deskripsi terlalu panjang
                                        if (count($sanksi_by_laporan_id[$current_laporan_id]) > 1 || strlen($sanksi_by_laporan_id[$current_laporan_id][0]['deskripsi']) > 40) {
                                            $sanksi_json = htmlspecialchars(json_encode($sanksi_by_laporan_id[$current_laporan_id]), ENT_QUOTES, 'UTF-8');
                                            echo '<button class="text-blue-500 hover:underline text-xs mt-1 view-sanksi-btn"';
                                            echo ' data-laporan-id="' . htmlspecialchars($laporan['id']) . '"';
                                            echo " data-sanksi-data='" . $sanksi_json . "'>";
                                            // echo 'Lihat Detail Sanksi</button>';
                                        }
                                    } else {
                                        echo 'Tidak ada sanksi';
                                    }
                                    ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['created_at'] ? date('d M Y H:i', strtotime($laporan['created_at'])) : 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($laporan['updated_at'] ? date('d M Y H:i', strtotime($laporan['updated_at'])) : 'N/A') ?>
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

    <div id="videoModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center hidden z-[1000]">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-xl max-w-2xl w-full relative">
            <button id="closeVideoModal"
                class="absolute top-2 right-2 text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white text-2xl font-bold leading-none">&times;</button>
            <h3 id="videoModalTitle" class="text-xl font-semibold text-gray-800 dark:text-white mb-4 pr-10"></h3>
            <div class="aspect-video w-full">
                <video id="videoPlayer" class="w-full h-full rounded" controls preload="auto"></video>
            </div>
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

<script src="https://cdn.jsdelivr.net/npm/simple-lightbox@2.14.2/dist/simple-lightbox.min.js"></script>
<script src="../../assets/js/DataLaporan.js"></script>


<?php include 'footer.php'; ?>