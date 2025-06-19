<?php

// Initialize variables for messages (still useful for general page errors)
$success_message = '';
$error_message = '';

// Check for messages passed via session (from previous operations)
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


// --- Fetch All Guru Data for Display ---
$guru_data = [];

if (empty($error_message) && isset($conn) && $conn->ping()) {
    $sql_fetch_guru = "
        SELECT
            u.id, u.nis_nip, u.nama, u.foto_profile, u.created_at, u.updated_at,
            jg.nama_jabatan
        FROM users u
        INNER JOIN guru g ON u.id = g.user_id
        LEFT JOIN jabatan_guru jg ON g.jabatan_id = jg.id
        WHERE u.role = 'guru'
        ORDER BY u.nama ASC
    ";
    $result_fetch_guru = mysqli_query($conn, $sql_fetch_guru);

    if ($result_fetch_guru) {
        while ($row = mysqli_fetch_assoc($result_fetch_guru)) {
            // Online status and last_seen_at logic removed as these columns are not displayed.
            $guru_data[] = $row;
        }
        mysqli_free_result($result_fetch_guru);
    } else {
        $error_message = 'Gagal mengambil data guru: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}