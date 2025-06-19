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
// This block will process the form submissions for managing 'kelas' data.
// We'll use a hidden 'action' field in the form to determine the operation.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas'] ?? '');
            if (!empty($nama_kelas)) {
                // TIDAK menyertakan created_at dan updated_at
                $sql = "INSERT INTO kelas (nama_kelas) VALUES ('$nama_kelas')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Kelas berhasil ditambahkan!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menambahkan kelas: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'Nama Kelas tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id_kelas = mysqli_real_escape_string($conn, $_POST['id_kelas'] ?? '');
            $nama_kelas = mysqli_real_escape_string($conn, $_POST['nama_kelas'] ?? '');
            if (!empty($id_kelas) && !empty($nama_kelas)) {
                // FIX: Menghapus tanda '=' yang berlebihan. TIDAK menyertakan updated_at.
                $sql = "UPDATE kelas SET nama_kelas = '$nama_kelas' WHERE id = '$id_kelas'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Kelas berhasil diubah!';
                } else {
                    $_SESSION['error_message'] = 'Gagal mengubah kelas: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Kelas atau Nama Kelas tidak boleh kosong.';
            }
            break;

        case 'delete':
            $id_kelas = mysqli_real_escape_string($conn, $_POST['id_kelas'] ?? '');
            if (!empty($id_kelas)) {
                $sql = "DELETE FROM kelas WHERE id = '$id_kelas'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Kelas berhasil dihapus!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menghapus kelas: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Kelas tidak boleh kosong untuk menghapus.';
            }
            break;

        default:
            $_SESSION['error_message'] = 'Aksi tidak valid.';
            break;
    }

    // Redirect to prevent form resubmission and display messages
    // header('Location: Data_kelas.php'); // Pastikan ini adalah nama file yang benar
    // exit();
}

// --- Fetch All Kelas Data for Display ---
$kelas_data = [];
if (empty($error_message)) { // Only fetch if no database connection errors
    // Memilih kolom 'id' dan 'nama_kelas' saja
    $sql_fetch_kelas = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
    $result_fetch_kelas = mysqli_query($conn, $sql_fetch_kelas);

    if ($result_fetch_kelas) {
        while ($row = mysqli_fetch_assoc($result_fetch_kelas)) {
            $kelas_data[] = $row;
        }
    } else {
        $error_message = 'Gagal mengambil data kelas: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}