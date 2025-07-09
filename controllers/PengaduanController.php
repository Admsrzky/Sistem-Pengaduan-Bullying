<?php

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
    // Re-establish connection here if it was closed by header.php and needed for POST operations
    if (!isset($conn) || !$conn->ping()) {
        include '../../config/database.php'; // Path relative to this file
        if (!isset($conn) || $conn->connect_error) {
            $_SESSION['error_message'] = "Koneksi database gagal saat POST: " . ($conn->connect_error ?? 'Unknown error');
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

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

// --- Re-establish connection for fetching data if it was closed by POST handler ---
if (!isset($conn) || !$conn->ping()) {
    // Re-include config/database.php if connection was closed or not established
    include '../../config/database.php';
    if (!isset($conn) || $conn->connect_error) {
        $error_message = "Koneksi database gagal saat fetching data: " . ($conn->connect_error ?? 'Unknown error');
    }
}

// --- Fetch All Laporan Data for Display ---
$laporan_data = [];
$sanksi_by_laporan_id = []; // Initialize to store grouped sanctions

if (empty($error_message) && isset($conn) && $conn->ping()) {
    // 1. Fetch all sanksi data and group by laporan_id
    $sql_fetch_sanksi = "SELECT id, laporan_id, jenis_sanksi, deskripsi, tanggal_mulai, tanggal_selesai, diberikan_oleh FROM sanksi ORDER BY laporan_id, id ASC";
    $result_fetch_sanksi = mysqli_query($conn, $sql_fetch_sanksi);

    if ($result_fetch_sanksi) {
        while ($sanksi_row = mysqli_fetch_assoc($result_fetch_sanksi)) {
            $sanksi_by_laporan_id[$sanksi_row['laporan_id']][] = $sanksi_row;
        }
        mysqli_free_result($result_fetch_sanksi);
    } else {
        $error_message = 'Gagal mengambil data sanksi: ' . mysqli_error($conn);
    }


    // 2. Fetch all laporan data (main query)
    $sql_fetch_laporan = "
        SELECT
            l.id, l.kategori_id, l.kronologi, l.lokasi, l.tanggal_kejadian, l.bukti, l.status, l.created_at, l.updated_at,
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
