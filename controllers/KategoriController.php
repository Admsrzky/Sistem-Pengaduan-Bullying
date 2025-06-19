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


if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori'] ?? '');
            if (!empty($nama_kategori)) {
                // --- PERBAIKAN: Tambahkan pemeriksaan duplikasi nama kategori ---
                $check_sql = "SELECT id FROM kategori_laporan WHERE nama_kategori = '$nama_kategori'";
                $check_result = mysqli_query($conn, $check_sql);

                if (!$check_result) {
                    $_SESSION['error_message'] = 'Gagal memeriksa kategori: ' . mysqli_error($conn);
                    break; // Keluar dari switch
                }

                if (mysqli_num_rows($check_result) > 0) {
                    $_SESSION['error_message'] = 'Kategori dengan nama "' . htmlspecialchars($_POST['nama_kategori']) . '" sudah ada. Harap gunakan nama lain.';
                    mysqli_free_result($check_result); // Bebaskan hasil query
                    break; // Keluar dari switch
                }
                mysqli_free_result($check_result); // Pastikan hasil query dibebaskan

                // Lanjutkan dengan INSERT jika tidak ada duplikasi
                $sql = "INSERT INTO kategori_laporan (nama_kategori) VALUES ('$nama_kategori')";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Kategori berhasil ditambahkan!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menambahkan kategori: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'Nama Kategori tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori'] ?? '');
            $nama_kategori = mysqli_real_escape_string($conn, $_POST['nama_kategori'] ?? '');
            if (!empty($id_kategori) && !empty($nama_kategori)) {
                // Opsional: Anda bisa menambahkan cek duplikasi di sini juga,
                // untuk memastikan nama kategori yang diedit tidak menabrak nama kategori lain (kecuali dirinya sendiri).
                $check_sql = "SELECT id FROM kategori_laporan WHERE nama_kategori = '$nama_kategori' AND id != '$id_kategori'";
                $check_result = mysqli_query($conn, $check_sql);

                if (!$check_result) {
                    $_SESSION['error_message'] = 'Gagal memeriksa kategori saat edit: ' . mysqli_error($conn);
                    break;
                }

                if (mysqli_num_rows($check_result) > 0) {
                    $_SESSION['error_message'] = 'Nama kategori "' . htmlspecialchars($_POST['nama_kategori']) . '" sudah digunakan oleh kategori lain.';
                    mysqli_free_result($check_result);
                    break;
                }
                mysqli_free_result($check_result);

                $sql = "UPDATE kategori_laporan SET nama_kategori = '$nama_kategori' WHERE id = '$id_kategori'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Kategori berhasil diubah!';
                } else {
                    $_SESSION['error_message'] = 'Gagal mengubah kategori: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Kategori atau Nama Kategori tidak boleh kosong.';
            }
            break;

        case 'delete':
            $id_kategori = mysqli_real_escape_string($conn, $_POST['id_kategori'] ?? '');
            if (!empty($id_kategori)) {
                $check_laporan_sql = "SELECT COUNT(*) AS total_laporan FROM laporan WHERE kategori_id = '$id_kategori'";
                $check_laporan_result = mysqli_query($conn, $check_laporan_sql);
                if ($check_laporan_result && mysqli_fetch_assoc($check_laporan_result)['total_laporan'] > 0) {
                    $_SESSION['error_message'] = 'Kategori tidak dapat dihapus karena masih ada laporan yang menggunakannya.';
                    mysqli_free_result($check_laporan_result);
                    break;
                }
                if ($check_laporan_result) mysqli_free_result($check_laporan_result);


                $sql = "DELETE FROM kategori_laporan WHERE id = '$id_kategori'";
                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Kategori berhasil dihapus!';
                } else {
                    $_SESSION['error_message'] = 'Gagal menghapus kategori: ' . mysqli_error($conn);
                }
            } else {
                $_SESSION['error_message'] = 'ID Kategori tidak boleh kosong untuk menghapus.';
            }
            break;

        default:
            $_SESSION['error_message'] = 'Aksi tidak valid.';
            break;
    }

    // Redirect to prevent form resubmission and display messages
    // header('Location: data_kategori_laporan.php'); // Pastikan ini adalah nama file yang benar
    // exit();
}

// --- Fetch All Kategori Data for Display ---
$kategori_data = [];
if (empty($error_message) && isset($conn)) { // Only fetch if no database connection errors and connection is valid
    $sql_fetch_kategori = "SELECT id, nama_kategori FROM kategori_laporan ORDER BY nama_kategori ASC";
    $result_fetch_kategori = mysqli_query($conn, $sql_fetch_kategori);

    if ($result_fetch_kategori) {
        while ($row = mysqli_fetch_assoc($result_fetch_kategori)) {
            $kategori_data[] = $row;
        }
        mysqli_free_result($result_fetch_kategori); // Free result set
    } else {
        $error_message = 'Gagal mengambil data kategori: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}