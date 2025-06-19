<?php

// Initialize variables for messages (still useful for general page errors)
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

// Path for profile pictures
$asset_base_path_users = '../../assets/img/profile/';

if (empty($error_message) && isset($conn) && $conn->ping()) {
    $sql_fetch_siswa = "
        SELECT
            u.id, u.nis_nip, u.nama, u.foto_profile, u.created_at, u.updated_at,
            s.jurusan_id, s.kelas_id,
            j.nama_jurusan,
            k.nama_kelas
        FROM users u
        INNER JOIN siswa s ON u.id = s.user_id
        LEFT JOIN jurusan j ON s.jurusan_id = j.id
        LEFT JOIN kelas k ON s.kelas_id = k.id
        WHERE u.role = 'siswa'
        ORDER BY u.nama ASC
    ";
    $result_fetch_siswa = mysqli_query($conn, $sql_fetch_siswa);

    if ($result_fetch_siswa) {
        while ($row = mysqli_fetch_assoc($result_fetch_siswa)) {
            // Online status logic removed as 'Status' column is not displayed.
            // 'last_seen_at' is also removed from SELECT as it's no longer used here.
            $siswa_data[] = $row;
        }
        mysqli_free_result($result_fetch_siswa);
    } else {
        $error_message = 'Gagal mengambil data siswa: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}