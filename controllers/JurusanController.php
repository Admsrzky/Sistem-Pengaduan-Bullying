<?php
include '../../config/database.php';

/**
 * =================================================================
 * JurusanController.php (Versi Notifikasi via URL)
 * =================================================================
 * Controller ini menangani logika data jurusan dan mengirimkan status
 * notifikasi melalui parameter URL (GET) setelah setiap aksi.
 */

// session_start() tidak lagi diperlukan untuk notifikasi ini.

// Diasumsikan koneksi database '$conn' sudah tersedia dari file yang meng-include controller ini.

// =================================================================
// BAGIAN PEMROSESAN FORM (CREATE, UPDATE, DELETE)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($conn) || $conn->connect_error) {
        $errorMsg = urlencode("Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error'));
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=error&msg=' . $errorMsg);
        exit();
    }

    $action = $_POST['action'] ?? '';
    $redirectUrl = $_SERVER['PHP_SELF']; // Halaman tujuan redirect

    switch ($action) {
        case 'add':
            $nama_jurusan = trim($_POST['nama_jurusan'] ?? '');
            if (!empty($nama_jurusan)) {
                $safe_nama_jurusan = mysqli_real_escape_string($conn, $nama_jurusan);
                $sql = "INSERT INTO jurusan (nama_jurusan) VALUES ('$safe_nama_jurusan')";

                if (mysqli_query($conn, $sql)) {
                    $status = 'success';
                    $msg = 'Jurusan baru berhasil ditambahkan!';
                } else {
                    $status = 'error';
                    $msg = 'Gagal menambahkan jurusan: ' . mysqli_error($conn);
                }
            } else {
                $status = 'error';
                $msg = 'Nama Jurusan tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id_jurusan = trim($_POST['id_jurusan'] ?? '');
            $nama_jurusan = trim($_POST['nama_jurusan'] ?? '');

            if (!empty($id_jurusan) && !empty($nama_jurusan)) {
                $safe_id_jurusan = mysqli_real_escape_string($conn, $id_jurusan);
                $safe_nama_jurusan = mysqli_real_escape_string($conn, $nama_jurusan);
                $sql = "UPDATE jurusan SET nama_jurusan = '$safe_nama_jurusan' WHERE id = '$safe_id_jurusan'";

                if (mysqli_query($conn, $sql)) {
                    $status = 'success';
                    $msg = 'Data Jurusan berhasil diubah!';
                } else {
                    $status = 'error';
                    $msg = 'Gagal mengubah data jurusan: ' . mysqli_error($conn);
                }
            } else {
                $status = 'error';
                $msg = 'ID atau Nama Jurusan tidak boleh kosong.';
            }
            break;

        case 'delete':
            $id_jurusan = trim($_POST['id_jurusan'] ?? '');
            if (!empty($id_jurusan)) {
                $safe_id_jurusan = mysqli_real_escape_string($conn, $id_jurusan);
                $sql = "DELETE FROM jurusan WHERE id = '$safe_id_jurusan'";

                if (mysqli_query($conn, $sql)) {
                    $status = 'success';
                    $msg = 'Jurusan berhasil dihapus!';
                } else {
                    $status = 'error';
                    $msg = 'Gagal menghapus jurusan.';
                }
            } else {
                $status = 'error';
                $msg = 'ID Jurusan tidak valid untuk penghapusan.';
            }
            break;

        default:
            $status = 'error';
            $msg = 'Aksi yang diminta tidak valid.';
            break;
    }

    // Redirect ke halaman tampilan dengan membawa status dan pesan
    // urlencode() penting untuk memastikan pesan tidak merusak URL
    header('Location: ' . $redirectUrl . '?status=' . $status . '&msg=' . urlencode($msg));
    exit();
}


// =================================================================
// BAGIAN PENGAMBILAN DATA (READ)
// =================================================================
$jurusan_data = [];
$fetch_error = '';

if (!isset($conn) || $conn->connect_error) {
    $fetch_error = "Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error');
} else {
    $sql_fetch = "SELECT id, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC";
    $result = mysqli_query($conn, $sql_fetch);

    if ($result) {
        $jurusan_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $fetch_error = 'Gagal mengambil data jurusan: ' . mysqli_error($conn);
    }
}
