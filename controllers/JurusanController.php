<?php

// Initialize variables for messages
$success_message = '';
$error_message = '';

// Check for messages passed via session (from previous operations like add/edit/delete)
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

// --- Handle Form Submissions (Add/Edit/Delete) ---
// This block will process the form submissions for managing 'jurusan' data.
// We'll use a hidden 'action' field in the form to determine the operation.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $nama_jurusan = mysqli_real_escape_string($conn, $_POST['nama_jurusan'] ?? '');
            if (!empty($nama_jurusan)) {
                $sql = "INSERT INTO jurusan (nama_jurusan) VALUES ('$nama_jurusan')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Jurusan berhasil ditambahkan!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menambahkan jurusan: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'Nama Jurusan tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id_jurusan = mysqli_real_escape_string($conn, $_POST['id_jurusan'] ?? '');
            $nama_jurusan = mysqli_real_escape_string($conn, $_POST['nama_jurusan'] ?? '');
            if (!empty($id_jurusan) && !empty($nama_jurusan)) {
                $sql = "UPDATE jurusan SET nama_jurusan = '$nama_jurusan' WHERE id = '$id_jurusan'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Jurusan berhasil diubah!';
                } else {
                    $_SESSION['error_message'] = 'Gagal mengubah jurusan: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Jurusan atau Nama Jurusan tidak boleh kosong.';
            }
            break;

        case 'delete':
            $id_jurusan = mysqli_real_escape_string($conn, $_POST['id_jurusan'] ?? '');
            if (!empty($id_jurusan)) {
                $sql = "DELETE FROM jurusan WHERE id = '$id_jurusan'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Jurusan berhasil dihapus!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menghapus jurusan: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Jurusan tidak boleh kosong untuk menghapus.';
            }
            break;

        default:
            $_SESSION['error_message'] = 'Aksi tidak valid.';
            break;
    }

    // Redirect to prevent form resubmission and display messages
    // header('Location: Data_jurusan.php'); // Pastikan ini adalah nama file yang benar
    // exit();
}

// --- Fetch All Jurusan Data for Display ---
$jurusan_data = [];
if (empty($error_message)) { // Only fetch if no database connection errors
    // Memilih kolom 'id' tetapi tidak menampilkannya di tabel
    $sql_fetch_jurusan = "SELECT id, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC";
    $result_fetch_jurusan = mysqli_query($conn, $sql_fetch_jurusan);

    if ($result_fetch_jurusan) {
        while ($row = mysqli_fetch_assoc($result_fetch_jurusan)) {
            $jurusan_data[] = $row;
        }
    } else {
        $error_message = 'Gagal mengambil data jurusan: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}