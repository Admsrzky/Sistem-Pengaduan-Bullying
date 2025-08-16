<?php
include '../../config/database.php';

/**
 * =================================================================
 * LaporanController.php
 * (Menggunakan Notifikasi via URL & Pola PRG)
 * =================================================================
 */

// Asumsikan koneksi '$conn' sudah tersedia dari file yang meng-include controller ini.
$asset_base_path_bukti_server = '../../uploads/'; // Path server untuk menyimpan file

// --- Fetch Master Data for Dropdowns ---
$kategori_options = [];
$status_options = ['terkirim', 'diproses', 'selesai', 'ditolak'];
$fetch_master_error = '';

if (isset($conn) && $conn->ping()) {
    // Bagian ini sudah benar, mengambil data kategori untuk dropdown di modal edit
    $result_kategori = mysqli_query($conn, "SELECT id, nama_kategori FROM kategori_laporan ORDER BY nama_kategori ASC");
    if ($result_kategori) {
        $kategori_options = mysqli_fetch_all($result_kategori, MYSQLI_ASSOC);
    } else {
        $fetch_master_error = "Gagal mengambil data kategori.";
    }
} else {
    $fetch_master_error = "Koneksi database gagal.";
}

// --- Handle Form Submissions (Edit/Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($conn)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=error&msg=' . urlencode("Koneksi database tidak ditemukan."));
        exit();
    }

    $action = $_POST['action'] ?? '';
    $status = 'error'; // Default status
    $msg = 'Aksi tidak diketahui atau terjadi kesalahan.'; // Default message

    mysqli_begin_transaction($conn);
    try {
        switch ($action) {
            case 'edit':
                $laporan_id = filter_input(INPUT_POST, 'edit_laporan_id', FILTER_VALIDATE_INT);
                $kategori_id = filter_input(INPUT_POST, 'edit_kategori_id', FILTER_VALIDATE_INT);
                $kronologi = $_POST['edit_kronologi'] ?? '';
                $lokasi = $_POST['edit_lokasi'] ?? '';
                $tanggal_kejadian = $_POST['edit_tanggal_kejadian'] ?? '';
                $status_laporan = $_POST['edit_status'] ?? '';
                $current_bukti_filename = $_POST['current_bukti_file'] ?? '';

                if (!$laporan_id || !$kategori_id || empty($kronologi) || empty($lokasi) || empty($tanggal_kejadian) || empty($status_laporan)) {
                    throw new Exception('Semua field wajib diisi untuk mengedit laporan.');
                }

                $update_bukti_sql_part = "";
                $bukti_filename_for_db = $current_bukti_filename;

                if (isset($_FILES['edit_bukti_file']) && $_FILES['edit_bukti_file']['error'] === UPLOAD_ERR_OK) {
                    $new_file_name = uniqid('bukti_') . '.' . strtolower(pathinfo($_FILES['edit_bukti_file']['name'], PATHINFO_EXTENSION));
                    if (!move_uploaded_file($_FILES['edit_bukti_file']['tmp_name'], $asset_base_path_bukti_server . $new_file_name)) {
                        throw new Exception('Gagal mengunggah file bukti baru.');
                    }
                    if (!empty($current_bukti_filename) && file_exists($asset_base_path_bukti_server . $current_bukti_filename)) {
                        unlink($asset_base_path_bukti_server . $current_bukti_filename);
                    }
                    $bukti_filename_for_db = $new_file_name;
                    $update_bukti_sql_part = ", bukti = ?";
                }

                $sql_update = "UPDATE laporan SET kategori_id = ?, kronologi = ?, lokasi = ?, tanggal_kejadian = ?, status = ?, updated_at = NOW() {$update_bukti_sql_part} WHERE id = ?";
                $stmt_update = $conn->prepare($sql_update);

                if ($update_bukti_sql_part) {
                    $stmt_update->bind_param("isssssi", $kategori_id, $kronologi, $lokasi, $tanggal_kejadian, $status_laporan, $bukti_filename_for_db, $laporan_id);
                } else {
                    $stmt_update->bind_param("issssi", $kategori_id, $kronologi, $lokasi, $tanggal_kejadian, $status_laporan, $laporan_id);
                }

                if (!$stmt_update->execute()) {
                    throw new Exception('Gagal memperbarui laporan: ' . $stmt_update->error);
                }

                $status = 'success';
                $msg = "Laporan berhasil diperbarui!";
                break;

            case 'delete':
                $laporan_id = filter_input(INPUT_POST, 'delete_laporan_id', FILTER_VALIDATE_INT);
                if (!$laporan_id) {
                    throw new Exception('ID laporan tidak valid untuk dihapus.');
                }

                $stmt_get_bukti = $conn->prepare("SELECT bukti FROM laporan WHERE id = ?");
                $stmt_get_bukti->bind_param("i", $laporan_id);
                $stmt_get_bukti->execute();
                $bukti_filename = $stmt_get_bukti->get_result()->fetch_assoc()['bukti'] ?? null;
                $stmt_get_bukti->close();

                $stmt_delete = $conn->prepare("DELETE FROM laporan WHERE id = ?");
                $stmt_delete->bind_param("i", $laporan_id);
                if (!$stmt_delete->execute()) {
                    throw new Exception('Gagal menghapus laporan: ' . $stmt_delete->error);
                }

                if (!empty($bukti_filename) && file_exists($asset_base_path_bukti_server . $bukti_filename)) {
                    unlink($asset_base_path_bukti_server . $bukti_filename);
                }

                $status = 'success';
                $msg = "Laporan berhasil dihapus!";
                break;

            default:
                throw new Exception('Aksi tidak valid.');
        }
        mysqli_commit($conn);
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $status = 'error';
        $msg = $e->getMessage();
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?status=' . $status . '&msg=' . urlencode($msg));
    exit();
}

// --- Fetch All Laporan Data for Display ---
$laporan_data = [];
$sanksi_by_laporan_id = [];
$fetch_error = '';

if (empty($fetch_master_error) && isset($conn) && $conn->ping()) {
    // 1. Fetch all sanksi data
    $result_sanksi = mysqli_query($conn, "SELECT id, laporan_id, jenis_sanksi FROM sanksi");
    if ($result_sanksi) {
        while ($sanksi_row = mysqli_fetch_assoc($result_sanksi)) {
            $sanksi_by_laporan_id[$sanksi_row['laporan_id']][] = $sanksi_row;
        }
    } else {
        $fetch_error = 'Gagal mengambil data sanksi.';
    }

    // 2. Fetch all laporan data
    $sql_laporan = "
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
    $result_laporan = mysqli_query($conn, $sql_laporan);
    if ($result_laporan) {
        $laporan_data = mysqli_fetch_all($result_laporan, MYSQLI_ASSOC);
    } else {
        $fetch_error .= ' Gagal mengambil data laporan.';
    }
} else {
    $fetch_error = $fetch_master_error ?: "Koneksi database tidak tersedia.";
}
