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

// Define the web path for BUKTI (URL) relative to the current file
// Current file: Sipeng/views/admin/data_laporan.php
// Uploads folder: Sipeng/uploads/
// To go from data_laporan.php to uploads/: ../../../uploads/
$asset_base_path_bukti_web = '../../uploads/';

// Define the server path for file operations (absolute path on the server)
// Using __DIR__ for robust path resolution.
// This assumes 'uploads' is directly under the 'Sipeng' root directory.
$asset_base_path_bukti_server = realpath(__DIR__ . '/../../uploads/') . DIRECTORY_SEPARATOR;

// --- Fetch Kategori Options for the Edit Modal ---
$kategori_options = [];
if (empty($error_message) && isset($conn) && $conn->ping()) {
    $sql_fetch_kategori = "SELECT id, nama_kategori FROM kategori_laporan ORDER BY nama_kategori ASC";
    $result_fetch_kategori = mysqli_query($conn, $sql_fetch_kategori);
    if ($result_fetch_kategori) {
        while ($row = mysqli_fetch_assoc($result_fetch_kategori)) {
            $kategori_options[] = $row;
        }
        mysqli_free_result($result_fetch_kategori);
    } else {
        $error_message = 'Gagal mengambil data kategori: ' . mysqli_error($conn);
    }
}

// Define Status Options for the Edit Modal
$status_options = ['terkirim', 'diproses', 'selesai', 'ditolak'];


// --- Handle Form Submissions (Edit and Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'edit') {
            // EDIT LOGIC
            $laporan_id = filter_input(INPUT_POST, 'edit_laporan_id', FILTER_SANITIZE_NUMBER_INT);
            $kategori_id = filter_input(INPUT_POST, 'edit_kategori_id', FILTER_SANITIZE_NUMBER_INT);
            $kronologi = filter_input(INPUT_POST, 'edit_kronologi', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $lokasi = filter_input(INPUT_POST, 'edit_lokasi', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $tanggal_kejadian = filter_input(INPUT_POST, 'edit_tanggal_kejadian', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $status = filter_input(INPUT_POST, 'edit_status', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $clear_bukti_file = isset($_POST['clear_bukti_file']) ? true : false;
            $current_bukti_filename = filter_input(INPUT_POST, 'current_bukti_file', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

            if (empty($laporan_id) || empty($kategori_id) || empty($kronologi) || empty($lokasi) || empty($tanggal_kejadian) || empty($status)) {
                $_SESSION['error_message'] = "Semua field wajib diisi untuk mengedit laporan.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            $update_bukti_sql = "";
            $bukti_filename_for_db = $current_bukti_filename; // Default to current filename

            // Handle file upload
            if (isset($_FILES['edit_bukti_file']) && $_FILES['edit_bukti_file']['error'] === UPLOAD_ERR_OK) {
                $file_tmp_name = $_FILES['edit_bukti_file']['tmp_name'];
                $file_name = basename($_FILES['edit_bukti_file']['name']);
                $file_size = $_FILES['edit_bukti_file']['size'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'webm', 'ogg', 'mov', 'avi', 'flv', '3gp', 'wmv', 'pdf'];
                $max_file_size = 50 * 1024 * 1024; // 50MB

                if (!in_array($file_ext, $allowed_extensions)) {
                    $_SESSION['error_message'] = "Format file bukti tidak diizinkan. Hanya gambar, video, atau PDF.";
                } elseif ($file_size > $max_file_size) {
                    $_SESSION['error_message'] = "Ukuran file bukti terlalu besar (maks 50MB).";
                } else {
                    // Generate unique filename
                    $new_file_name = uniqid('bukti_') . '.' . $file_ext;
                    $upload_path = $asset_base_path_bukti_server . $new_file_name;

                    if (move_uploaded_file($file_tmp_name, $upload_path)) {
                        // Delete old file if it exists and a new one is uploaded
                        if (!empty($current_bukti_filename) && file_exists($asset_base_path_bukti_server . $current_bukti_filename)) {
                            unlink($asset_base_path_bukti_server . $current_bukti_filename);
                        }
                        $bukti_filename_for_db = $new_file_name;
                        $update_bukti_sql = ", bukti = ?";
                    } else {
                        $_SESSION['error_message'] = "Gagal mengunggah file bukti baru.";
                    }
                }
            } elseif ($clear_bukti_file && !empty($current_bukti_filename)) {
                // User chose to clear the existing file
                if (file_exists($asset_base_path_bukti_server . $current_bukti_filename)) {
                    unlink($asset_base_path_bukti_server . $current_bukti_filename);
                }
                $bukti_filename_for_db = NULL; // Set bukti to NULL in DB
                $update_bukti_sql = ", bukti = NULL";
            }

            if (empty($_SESSION['error_message'])) { // Proceed only if no file upload errors
                // Get current values from hidden fields for comparison
                $current_kategori_id = filter_input(INPUT_POST, 'current_kategori_id_display', FILTER_SANITIZE_NUMBER_INT);
                $current_kronologi = filter_input(INPUT_POST, 'current_kronologi_display', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $current_lokasi = filter_input(INPUT_POST, 'current_lokasi_display', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $current_tanggal_kejadian = filter_input(INPUT_POST, 'current_tanggal_kejadian_display', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                $current_status = filter_input(INPUT_POST, 'current_status_display', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

                // Check if any data has actually changed
                $data_changed = false;
                if (
                    $kategori_id != $current_kategori_id ||
                    $kronologi != $current_kronologi ||
                    $lokasi != $current_lokasi ||
                    $tanggal_kejadian != $current_tanggal_kejadian ||
                    $status != $current_status ||
                    $update_bukti_sql !== "" || // New file uploaded or old one cleared
                    ($bukti_filename_for_db !== $current_bukti_filename && $update_bukti_sql == "") // Filename changed without new upload (e.g. if current_bukti_filename was empty and now it's not, or vice versa)
                ) {
                    $data_changed = true;
                }

                if ($data_changed) {
                    $sql_update_laporan = "UPDATE laporan SET kategori_id = ?, kronologi = ?, lokasi = ?, tanggal_kejadian = ?, status = ?, updated_at = NOW() {$update_bukti_sql} WHERE id = ?";
                    $stmt_update = mysqli_prepare($conn, $sql_update_laporan);

                    if ($stmt_update) {
                        if ($update_bukti_sql !== "") {
                            mysqli_stmt_bind_param($stmt_update, "isssssi", $kategori_id, $kronologi, $lokasi, $tanggal_kejadian, $status, $bukti_filename_for_db, $laporan_id);
                        } else {
                            mysqli_stmt_bind_param($stmt_update, "issssi", $kategori_id, $kronologi, $lokasi, $tanggal_kejadian, $status, $laporan_id);
                        }

                        if (mysqli_stmt_execute($stmt_update)) {
                            $_SESSION['success_message'] = "Laporan berhasil diperbarui!";
                        } else {
                            $_SESSION['error_message'] = "Gagal memperbarui laporan: " . mysqli_error($conn);
                        }
                        mysqli_stmt_close($stmt_update);
                    } else {
                        $_SESSION['error_message'] = "Gagal menyiapkan statement update laporan: " . mysqli_error($conn);
                    }
                } else {
                    $_SESSION['success_message'] = "Tidak ada perubahan yang terdeteksi pada laporan.";
                }
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        } elseif ($_POST['action'] === 'delete') {
            // DELETE LOGIC
            $laporan_id = filter_input(INPUT_POST, 'delete_laporan_id', FILTER_SANITIZE_NUMBER_INT);

            if (empty($laporan_id)) {
                $_SESSION['error_message'] = "ID laporan tidak valid untuk dihapus.";
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit;
            }

            // First, get the filename to delete the actual file
            $sql_get_bukti = "SELECT bukti FROM laporan WHERE id = ?";
            $stmt_get_bukti = mysqli_prepare($conn, $sql_get_bukti);
            if ($stmt_get_bukti) {
                mysqli_stmt_bind_param($stmt_get_bukti, "i", $laporan_id);
                mysqli_stmt_execute($stmt_get_bukti);
                mysqli_stmt_bind_result($stmt_get_bukti, $bukti_filename);
                mysqli_stmt_fetch($stmt_get_bukti);
                mysqli_stmt_close($stmt_get_bukti);

                // Now delete the record from the database
                $sql_delete_laporan = "DELETE FROM laporan WHERE id = ?";
                $stmt_delete = mysqli_prepare($conn, $sql_delete_laporan);

                if ($stmt_delete) {
                    mysqli_stmt_bind_param($stmt_delete, "i", $laporan_id);
                    if (mysqli_stmt_execute($stmt_delete)) {
                        // If DB record deleted, then delete the file
                        if (!empty($bukti_filename) && file_exists($asset_base_path_bukti_server . $bukti_filename)) {
                            unlink($asset_base_path_bukti_server . $bukti_filename);
                        }
                        $_SESSION['success_message'] = "Laporan berhasil dihapus!";
                    } else {
                        $_SESSION['error_message'] = "Gagal menghapus laporan dari database: " . mysqli_error($conn);
                    }
                    mysqli_stmt_close($stmt_delete);
                } else {
                    $_SESSION['error_message'] = "Gagal menyiapkan statement delete laporan: " . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = "Gagal mengambil nama file bukti untuk dihapus: " . mysqli_error($conn);
            }
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}


// --- Fetch All Laporan Data for Display ---
$laporan_data = [];

if (empty($error_message) && isset($conn) && $conn->ping()) {
    $sql_fetch_laporan = "
        SELECT
            l.id, l.kronologi, l.lokasi, l.tanggal_kejadian, l.bukti, l.status, l.created_at, l.updated_at,
            kl.nama_kategori,
            u.nama AS nama_pelapor,
            u.nis_nip AS nisnip_pelapor
        FROM laporan l
        LEFT JOIN kategori_laporan kl ON l.kategori_id = kl.id
        LEFT JOIN users u ON l.user_id = u.id
        ORDER BY l.created_at DESC
    ";
    $result_fetch_laporan = mysqli_query($conn, $sql_fetch_laporan);

    if ($result_fetch_laporan) {
        while ($row = mysqli_fetch_assoc($result_fetch_laporan)) {
            $laporan_data[] = $row;
        }
        mysqli_free_result($result_fetch_laporan);
    } else {
        $error_message = 'Gagal mengambil data laporan: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}
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
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
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

</main>

<script src="https://cdn.jsdelivr.net/npm/simple-lightbox@2.14.2/dist/simple-lightbox.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Re-initialize feather icons

    const editButtons = document.querySelectorAll('.edit-btn');
    const editLaporanModal = document.getElementById('editLaporanModal');
    const closeEditModalButton = document.getElementById('closeEditModal');

    const editLaporanIdInput = document.getElementById('edit_laporan_id');
    const editKategoriIdSelect = document.getElementById('edit_kategori_id');
    const editKronologiTextarea = document.getElementById('edit_kronologi');
    const editLokasiInput = document.getElementById('edit_lokasi');
    const editTanggalKejadianInput = document.getElementById('edit_tanggal_kejadian');
    const editStatusSelect = document.getElementById('edit_status');
    const editBuktiFileInput = document.getElementById('edit_bukti_file');
    const currentBuktiFileHidden = document.getElementById('current_bukti_file');
    const currentBuktiDisplay = document.getElementById('current_bukti_display');
    const currentBuktiPreviewContainer = document.getElementById('current_bukti_preview_container');
    const clearBuktiFileCheckbox = document.getElementById('clear_bukti_file_checkbox');


    // Hidden fields for comparison (populated with original values)
    const currentKategoriIdDisplay = document.getElementById('current_kategori_id_display');
    const currentKronologiDisplay = document.getElementById('current_kronologi_display');
    const currentLokasiDisplay = document.getElementById('current_lokasi_display');
    const currentTanggalKejadianDisplay = document.getElementById('current_tanggal_kejadian_display');
    const currentStatusDisplay = document.getElementById('current_status_display');


    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const kategoriId = this.dataset.kategoriId;
            const kronologi = this.dataset.kronologi;
            const lokasi = this.dataset.lokasi;
            const tanggalKejadian = this.dataset.tanggalKejadian; // Format YYYY-MM-DD
            const bukti = this.dataset.bukti; // This will be the filename
            const status = this.dataset.status;

            // Populate form fields
            editLaporanIdInput.value = id;
            editKategoriIdSelect.value = kategoriId;
            editKronologiTextarea.value = kronologi;
            editLokasiInput.value = lokasi;
            editTanggalKejadianInput.value =
                tanggalKejadian; // HTML date input expects YYYY-MM-DD
            editStatusSelect.value = status;

            // Store current values in hidden fields for comparison
            currentKategoriIdDisplay.value = kategoriId;
            currentKronologiDisplay.value = kronologi;
            currentLokasiDisplay.value = lokasi;
            currentTanggalKejadianDisplay.value = tanggalKejadian;
            currentBuktiFileHidden.value = bukti; // Store filename for backend
            currentStatusDisplay.value = status;


            // Display current bukti filename in the edit modal (no live preview)
            if (bukti) {
                // Construct the full web path for the current bukti file
                const fullBuktiWebPath = '<?= $asset_base_path_bukti_web ?>' + bukti;
                currentBuktiDisplay.innerHTML =
                    `<a href="${fullBuktiWebPath}" target="_blank">${bukti}</a>`;
                currentBuktiPreviewContainer.style.display = 'block';
                clearBuktiFileCheckbox.checked =
                    false; // Uncheck clear checkbox when displaying existing bukti
            } else {
                currentBuktiDisplay.innerHTML = 'N/A';
                currentBuktiPreviewContainer.style.display =
                    'block'; // Keep container visible even if N/A
                clearBuktiFileCheckbox.checked = false;
            }

            editLaporanModal.classList.remove('hidden');
        });
    });

    closeEditModalButton.addEventListener('click', function() {
        editLaporanModal.classList.add('hidden');
        // Clear form fields and reset previews/checkboxes
        editKronologiTextarea.value = '';
        editLokasiInput.value = '';
        editTanggalKejadianInput.value = '';
        editBuktiFileInput.value = ''; // Clear file input
        currentBuktiDisplay.innerHTML = 'N/A';
        currentBuktiFileHidden.value = '';
        currentBuktiPreviewContainer.style.display = 'none'; // Hide preview if no file
        clearBuktiFileCheckbox.checked = false;
        editKategoriIdSelect.value = '';
        editStatusSelect.value = '';
    });

    // Close modal if user clicks outside of it
    editLaporanModal.addEventListener('click', function(event) {
        if (event.target === editLaporanModal) {
            closeEditModalButton.click(); // Reuse click handler
        }
    });

    // Handle ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !editLaporanModal.classList.contains('hidden')) {
            closeEditModalButton.click(); // Reuse click handler
        }
    });

    // Optional: Reset clear bukti checkbox if a new file is chosen
    editBuktiFileInput.addEventListener('change', function() {
        if (this.value) { // If a new file is selected
            clearBuktiFileCheckbox.checked = false; // Ensure "clear" is not checked
        }
    });


    // --- Simple Lightbox Initialization ---
    // Initialize SimpleLightbox only on elements with the data-lightbox attribute
    var lightbox = new SimpleLightbox('.min-w-full a[data-lightbox]', {
        // Add any options you want here, e.g.:
        captionsData: 'title', // use data-title for captions
        captionDelay: 0,
        animationSlide: false
    });
});
</script>

<?php include 'footer.php'; ?>