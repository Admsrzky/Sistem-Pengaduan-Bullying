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
// This block will process the form submissions for managing 'jabatan_guru' data.
// We'll use a hidden 'action' field in the form to determine the operation.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $nama_jabatan = mysqli_real_escape_string($conn, $_POST['nama_jabatan'] ?? '');
            if (!empty($nama_jabatan)) {
                // --- Tambahkan pemeriksaan duplikasi nama jabatan ---
                $check_sql = "SELECT id FROM jabatan_guru WHERE nama_jabatan = '$nama_jabatan'";
                $check_result = mysqli_query($conn, $check_sql);

                if (!$check_result) {
                    $_SESSION['error_message'] = 'Gagal memeriksa jabatan: ' . mysqli_error($conn);
                    break;
                }

                if (mysqli_num_rows($check_result) > 0) {
                    $_SESSION['error_message'] = 'Jabatan dengan nama "' . htmlspecialchars($_POST['nama_jabatan']) . '" sudah ada. Harap gunakan nama lain.';
                    mysqli_free_result($check_result);
                    break;
                }
                mysqli_free_result($check_result);

                // Lanjutkan dengan INSERT jika tidak ada duplikasi
                $sql = "INSERT INTO jabatan_guru (nama_jabatan) VALUES ('$nama_jabatan')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Jabatan berhasil ditambahkan!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menambahkan jabatan: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'Nama Jabatan tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id_jabatan = mysqli_real_escape_string($conn, $_POST['id_jabatan'] ?? '');
            $nama_jabatan = mysqli_real_escape_string($conn, $_POST['nama_jabatan'] ?? '');
            if (!empty($id_jabatan) && !empty($nama_jabatan)) {
                // --- Tambahkan pemeriksaan duplikasi nama jabatan (kecuali dirinya sendiri) ---
                $check_sql = "SELECT id FROM jabatan_guru WHERE nama_jabatan = '$nama_jabatan' AND id != '$id_jabatan'";
                $check_result = mysqli_query($conn, $check_sql);

                if (!$check_result) {
                    $_SESSION['error_message'] = 'Gagal memeriksa jabatan saat edit: ' . mysqli_error($conn);
                    break;
                }

                if (mysqli_num_rows($check_result) > 0) {
                    $_SESSION['error_message'] = 'Nama jabatan "' . htmlspecialchars($_POST['nama_jabatan']) . '" sudah digunakan oleh jabatan lain.';
                    mysqli_free_result($check_result);
                    break;
                }
                mysqli_free_result($check_result);

                $sql = "UPDATE jabatan_guru SET nama_jabatan = '$nama_jabatan' WHERE id = '$id_jabatan'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Jabatan berhasil diubah!';
                } else {
                    $_SESSION['error_message'] = 'Gagal mengubah jabatan: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Jabatan atau Nama Jabatan tidak boleh kosong.';
            }
            break;

        case 'delete':
            $id_jabatan = mysqli_real_escape_string($conn, $_POST['id_jabatan'] ?? '');
            if (!empty($id_jabatan)) {
                // --- Saran: Tambahkan pengecekan integritas referensial ---
                // Sebelum menghapus jabatan, cek apakah ada guru yang menggunakan jabatan ini.
                $check_guru_sql = "SELECT COUNT(*) AS total_guru FROM guru WHERE jabatan_id = '$id_jabatan'";
                $check_guru_result = mysqli_query($conn, $check_guru_sql);
                if ($check_guru_result && mysqli_fetch_assoc($check_guru_result)['total_guru'] > 0) {
                    $_SESSION['error_message'] = 'Jabatan tidak dapat dihapus karena masih ada guru yang menggunakannya.';
                    mysqli_free_result($check_guru_result);
                    break;
                }
                if ($check_guru_result) mysqli_free_result($check_guru_result); // Pastikan resource dibebaskan

                $sql = "DELETE FROM jabatan_guru WHERE id = '$id_jabatan'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Jabatan berhasil dihapus!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menghapus jabatan: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Jabatan tidak boleh kosong untuk menghapus.';
            }
            break;

        default:
            $_SESSION['error_message'] = 'Aksi tidak valid.';
            break;
    }

    // Redirect to prevent form resubmission and display messages
    // header('Location: data_jabatan_guru.php'); // Uncomment this line and ensure the filename is correct
    // exit();
}

// --- Fetch All Jabatan Data for Display ---
$jabatan_data = [];
if (empty($error_message) && isset($conn)) { // Only fetch if no database connection errors and connection is valid
    $sql_fetch_jabatan_display = "SELECT id, nama_jabatan FROM jabatan_guru ORDER BY nama_jabatan ASC";
    $result_fetch_jabatan = mysqli_query($conn, $sql_fetch_jabatan_display);

    if ($result_fetch_jabatan) {
        while ($row = mysqli_fetch_assoc($result_fetch_jabatan)) {
            $jabatan_data[] = $row;
        }
        mysqli_free_result($result_fetch_jabatan); // Free result set
    } else {
        $error_message = 'Gagal mengambil data jabatan: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}