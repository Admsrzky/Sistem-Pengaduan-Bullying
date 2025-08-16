<?php
include '../../config/database.php';

// --- Handle Form Submissions (Add/Edit/Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($conn)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=error&msg=' . urlencode("Koneksi database tidak ditemukan."));
        exit();
    }

    $action = $_POST['action'] ?? '';
    $status = 'error'; // Default status
    $msg = 'Aksi tidak diketahui atau terjadi kesalahan.'; // Default message

    switch ($action) {
        case 'add':
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            if (!empty($nama_kategori)) {
                $safe_nama = mysqli_real_escape_string($conn, $nama_kategori);

                $check_sql = "SELECT id FROM kategori_laporan WHERE nama_kategori = '$safe_nama'";
                $check_result = mysqli_query($conn, $check_sql);

                if (mysqli_num_rows($check_result) > 0) {
                    $status = 'error';
                    $msg = 'Kategori "' . htmlspecialchars($nama_kategori) . '" sudah ada.';
                } else {
                    $sql = "INSERT INTO kategori_laporan (nama_kategori) VALUES ('$safe_nama')";
                    if (mysqli_query($conn, $sql)) {
                        $status = 'success';
                        $msg = 'Kategori baru berhasil ditambahkan!';
                    } else {
                        $status = 'error';
                        $msg = 'Gagal menambahkan kategori: ' . mysqli_error($conn);
                    }
                }
            } else {
                $status = 'error';
                $msg = 'Nama Kategori tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori'] ?? '');
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            if (!empty($id_kategori) && !empty($nama_kategori)) {
                $safe_nama = mysqli_real_escape_string($conn, $nama_kategori);

                $check_sql = "SELECT id FROM kategori_laporan WHERE nama_kategori = '$safe_nama' AND id != '$id_kategori'";
                $check_result = mysqli_query($conn, $check_sql);

                if (mysqli_num_rows($check_result) > 0) {
                    $status = 'error';
                    $msg = 'Nama kategori "' . htmlspecialchars($nama_kategori) . '" sudah digunakan.';
                } else {
                    $sql = "UPDATE kategori_laporan SET nama_kategori = '$safe_nama' WHERE id = '$id_kategori'";
                    if (mysqli_query($conn, $sql)) {
                        $status = 'success';
                        $msg = 'Kategori berhasil diubah!';
                    } else {
                        $status = 'error';
                        $msg = 'Gagal mengubah kategori: ' . mysqli_error($conn);
                    }
                }
            } else {
                $status = 'error';
                $msg = 'Data tidak lengkap untuk proses edit.';
            }
            break;

        case 'delete':
            $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori'] ?? '');
            if (!empty($id_kategori)) {
                $check_laporan_sql = "SELECT COUNT(*) AS total_laporan FROM laporan WHERE kategori_id = '$id_kategori'";
                $check_laporan_result = mysqli_query($conn, $check_laporan_sql);
                $laporan_count = mysqli_fetch_assoc($check_laporan_result)['total_laporan'];

                if ($laporan_count > 0) {
                    $status = 'error';
                    $msg = 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $laporan_count . ' laporan.';
                } else {
                    $sql = "DELETE FROM kategori_laporan WHERE id = '$id_kategori'";
                    if (mysqli_query($conn, $sql)) {
                        $status = 'success';
                        $msg = 'Kategori berhasil dihapus!';
                    } else {
                        $status = 'error';
                        $msg = 'Gagal menghapus kategori: ' . mysqli_error($conn);
                    }
                }
            } else {
                $status = 'error';
                $msg = 'ID Kategori tidak valid.';
            }
            break;

        default:
            $status = 'error';
            $msg = 'Aksi yang diminta tidak valid.';
            break;
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?status=' . $status . '&msg=' . urlencode($msg));
    exit();
}

// --- Fetch All Kategori Data for Display ---
$kategori_data = [];
$fetch_error = '';

if (isset($conn)) {
    $sql_fetch_kategori = "SELECT id, nama_kategori FROM kategori_laporan ORDER BY nama_kategori ASC";
    $result_fetch_kategori = mysqli_query($conn, $sql_fetch_kategori);

    if ($result_fetch_kategori) {
        $kategori_data = mysqli_fetch_all($result_fetch_kategori, MYSQLI_ASSOC);
    } else {
        $fetch_error = 'Gagal mengambil data kategori: ' . mysqli_error($conn);
    }
} else {
    $fetch_error = "Koneksi database tidak tersedia.";
}

// Koneksi akan ditutup oleh file footer.php