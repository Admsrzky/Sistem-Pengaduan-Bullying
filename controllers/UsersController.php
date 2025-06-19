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


// We'll proceed assuming $conn is valid here for database operations.
if (!isset($conn) || $conn->connect_error) {
    $error_message = "Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error');
}

// Path for profile pictures (from your header.php)
$asset_base_path_users = '../../assets/img/profile/'; // Ensure this path is correct for user profiles

// --- Fetch Master Data for dropdowns (Jabatan, Jurusan, Kelas) ---
$jabatan_options = [];
$jurusan_options = [];
$kelas_options = [];

if (empty($error_message) && isset($conn) && $conn->ping()) {
    // Fetch Jabatan
    $sql_fetch_jabatan = "SELECT id, nama_jabatan FROM jabatan_guru ORDER BY nama_jabatan ASC";
    $result_jabatan = mysqli_query($conn, $sql_fetch_jabatan);
    if ($result_jabatan) {
        while ($row = mysqli_fetch_assoc($result_jabatan)) {
            $jabatan_options[] = $row;
        }
        mysqli_free_result($result_jabatan);
    } else {
        error_log("Gagal mengambil data jabatan: " . mysqli_error($conn));
        $error_message = "Gagal mengambil data jabatan.";
    }

    // Fetch Jurusan
    $sql_fetch_jurusan = "SELECT id, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC";
    $result_jurusan = mysqli_query($conn, $sql_fetch_jurusan);
    if ($result_jurusan) {
        while ($row = mysqli_fetch_assoc($result_jurusan)) {
            $jurusan_options[] = $row;
        }
        mysqli_free_result($result_jurusan);
    } else {
        error_log("Gagal mengambil data jurusan: " . mysqli_error($conn));
        $error_message = "Gagal mengambil data jurusan.";
    }

    // FIX: Ambil hanya id dan nama_kelas dari tabel kelas sesuai struktur DB Anda.
    // Filter kelas berdasarkan jurusan TIDAK DAPAT dilakukan langsung di sini
    // karena jurusan_id tidak ada di tabel kelas.
    $sql_fetch_kelas = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
    $result_kelas = mysqli_query($conn, $sql_fetch_kelas);
    if ($result_kelas) {
        while ($row = mysqli_fetch_assoc($result_kelas)) {
            $kelas_options[] = $row;
        }
        mysqli_free_result($result_kelas);
    } else {
        error_log("Gagal mengambil data kelas: " . mysqli_error($conn));
        $error_message = "Gagal mengambil data kelas.";
    }
}


// --- Handle Form Submissions (Add/Edit/Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error_message)) {
    $action = $_POST['action'] ?? '';

    // Start transaction for POST operations that modify data
    mysqli_begin_transaction($conn);
    try {
        switch ($action) {
            case 'add':
                // --- PENTING: mysqli_real_escape_string() digunakan karena tidak ada prepared statements ---
                $new_nisnip = mysqli_real_escape_string($conn, $_POST['nis_nip'] ?? '');
                $new_nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
                $new_password_raw = $_POST['password'] ?? ''; // Raw password before hashing
                $new_role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');

                // Ambil nilai dari form add_user
                $new_jurusan_id = mysqli_real_escape_string($conn, $_POST['add_jurusan_id'] ?? '');
                $new_kelas_id = mysqli_real_escape_string($conn, $_POST['add_kelas_id'] ?? '');
                $new_jabatan_id = mysqli_real_escape_string($conn, $_POST['add_jabatan_id'] ?? '');

                if (empty($new_nisnip) || empty($new_nama) || empty($new_password_raw) || empty($new_role)) {
                    throw new Exception('Semua field wajib diisi untuk menambahkan user.');
                }
                // Validasi tambahan untuk role spesifik saat add
                if ($new_role === 'siswa' && (empty($new_jurusan_id) || empty($new_kelas_id))) {
                    throw new Exception('Jurusan dan Kelas wajib diisi untuk siswa baru.');
                }
                if ($new_role === 'guru' && empty($new_jabatan_id)) {
                    throw new Exception('Jabatan wajib diisi untuk guru baru.');
                }


                // Check for existing NIS/NIP
                $sql_check_nisnip = "SELECT nis_nip FROM users WHERE nis_nip = '{$new_nisnip}'";
                $result_check_nisnip = mysqli_query($conn, $sql_check_nisnip);
                if (!$result_check_nisnip) {
                    throw new Exception('Gagal memeriksa NIS/NIP: ' . mysqli_error($conn));
                }
                if (mysqli_num_rows($result_check_nisnip) > 0) {
                    throw new Exception('NIS/NIP tersebut sudah terdaftar.');
                }
                mysqli_free_result($result_check_nisnip);

                // Using password_hash for better security (MD5 is outdated and risky)
                $hashed_password = password_hash($new_password_raw, PASSWORD_DEFAULT);
                $hashed_password_escaped = mysqli_real_escape_string($conn, $hashed_password);

                $foto_profile_name = 'default.png';

                // Handle file upload
                if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
                    $target_dir = $asset_base_path_users;
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    $file_extension = pathinfo($_FILES['foto_profile']['name'], PATHINFO_EXTENSION);
                    $unique_file_name = uniqid('profile_') . '.' . $file_extension;
                    $target_file = $target_dir . $unique_file_name;
                    $imageFileType = strtolower($file_extension);

                    $check_img = getimagesize($_FILES['foto_profile']['tmp_name']);
                    if ($check_img !== false && in_array($imageFileType, ["jpg", "png", "jpeg", "gif"]) && $_FILES['foto_profile']['size'] <= 5000000) {
                        if (!move_uploaded_file($_FILES['foto_profile']['tmp_name'], $target_file)) {
                            throw new Exception('Gagal mengunggah foto profil. Error code: ' . $_FILES['foto_profile']['error']);
                        }
                        $foto_profile_name = mysqli_real_escape_string($conn, $unique_file_name);
                    } else {
                        throw new Exception('File foto tidak valid atau terlalu besar (maks 5MB, format JPG, JPEG, PNG, GIF).');
                    }
                }

                // Insert into 'users' table
                $sql_add_user = "INSERT INTO users (nis_nip, password, role, nama, foto_profile, created_at, updated_at) VALUES (
                    '{$new_nisnip}',
                    '{$hashed_password_escaped}',
                    '{$new_role}',
                    '{$new_nama}',
                    '{$foto_profile_name}',
                    CURRENT_TIMESTAMP,
                    CURRENT_TIMESTAMP
                )";

                if (!mysqli_query($conn, $sql_add_user)) {
                    throw new Exception('Gagal menambahkan user ke tabel users: ' . mysqli_error($conn));
                }
                $last_inserted_user_id = mysqli_insert_id($conn);

                // Insert into specific role tables (siswa or guru)
                if ($new_role === 'siswa') {
                    $sql_add_siswa = "INSERT INTO siswa (user_id, jurusan_id, kelas_id) VALUES (
                        '{$last_inserted_user_id}', 
                        " . (!empty($new_jurusan_id) ? "'{$new_jurusan_id}'" : "NULL") . ", 
                        " . (!empty($new_kelas_id) ? "'{$new_kelas_id}'" : "NULL") . "
                    )";
                    if (!mysqli_query($conn, $sql_add_siswa)) {
                        throw new Exception('Gagal menambahkan data ke tabel siswa: ' . mysqli_error($conn));
                    }
                } elseif ($new_role === 'guru') {
                    $sql_add_guru = "INSERT INTO guru (user_id, jabatan_id) VALUES (
                        '{$last_inserted_user_id}', 
                        " . (!empty($new_jabatan_id) ? "'{$new_jabatan_id}'" : "NULL") . "
                    )";
                    if (!mysqli_query($conn, $sql_add_guru)) {
                        throw new Exception('Gagal menambahkan data ke tabel guru: ' . mysqli_error($conn));
                    }
                }

                $_SESSION['success_message'] = 'User ' . htmlspecialchars(str_replace("'", "", $_POST['nama'])) . ' (NIS/NIP: ' . htmlspecialchars(str_replace("'", "", $_POST['nis_nip'])) . ') berhasil ditambahkan sebagai ' . htmlspecialchars(str_replace("'", "", $_POST['role'])) . '!';
                break;

            case 'edit':
                // --- PENTING: mysqli_real_escape_string() digunakan di setiap nilai input ---
                $edit_id = mysqli_real_escape_string($conn, $_POST['edit_id_user'] ?? '');
                $edit_nama = mysqli_real_escape_string($conn, $_POST['edit_nama'] ?? '');
                $edit_role = mysqli_real_escape_string($conn, $_POST['edit_role'] ?? '');
                $edit_new_password_raw = $_POST['edit_new_password'] ?? '';
                $edit_nisnip = mysqli_real_escape_string($conn, $_POST['edit_nisnip'] ?? '');

                $edit_jabatan_id = mysqli_real_escape_string($conn, $_POST['edit_jabatan_id'] ?? ''); // New
                $edit_jurusan_id = mysqli_real_escape_string($conn, $_POST['edit_jurusan_id'] ?? ''); // New
                $edit_kelas_id = mysqli_real_escape_string($conn, $_POST['edit_kelas_id'] ?? '');   // New

                // Hidden fields to store current values for comparison
                $current_nama_display = mysqli_real_escape_string($conn, $_POST['current_nama_display'] ?? '');
                $current_role_display = mysqli_real_escape_string($conn, $_POST['current_role_display'] ?? '');
                $current_foto_profile_display = mysqli_real_escape_string($conn, $_POST['current_foto_profile_display'] ?? 'default.png');
                $current_nisnip_display = mysqli_real_escape_string($conn, $_POST['current_nisnip_display'] ?? '');
                $current_jabatan_id_display = mysqli_real_escape_string($conn, $_POST['current_jabatan_id_display'] ?? '');
                $current_jurusan_id_display = mysqli_real_escape_string($conn, $_POST['current_jurusan_id_display'] ?? '');
                $current_kelas_id_display = mysqli_real_escape_string($conn, $_POST['current_kelas_id_display'] ?? '');


                if (empty($edit_id) || empty($edit_nama) || empty($edit_role) || empty($edit_nisnip)) {
                    throw new Exception('NIS/NIP, Nama, dan Role tidak boleh kosong untuk mengubah user.');
                }
                if ($edit_role === 'guru' && empty($edit_jabatan_id)) {
                    throw new Exception('Jabatan harus diisi jika role adalah Guru.');
                }
                if ($edit_role === 'siswa' && (empty($edit_jurusan_id) || empty($edit_kelas_id))) {
                    throw new Exception('Jurusan dan Kelas harus diisi jika role adalah Siswa.');
                }

                // Check if new NIS/NIP already exists for *another* user (if NIS/NIP was changed)
                if ($edit_nisnip !== $current_nisnip_display) {
                    $sql_check_nisnip_edit = "SELECT id FROM users WHERE nis_nip = '{$edit_nisnip}' AND id != '{$edit_id}'";
                    $result_check_nisnip_edit = mysqli_query($conn, $sql_check_nisnip_edit);
                    if (!$result_check_nisnip_edit) {
                        throw new Exception('Gagal memeriksa NIS/NIP baru: ' . mysqli_error($conn));
                    }
                    if (mysqli_num_rows($result_check_nisnip_edit) > 0) {
                        mysqli_free_result($result_check_nisnip_edit);
                        throw new Exception('NIS/NIP baru tersebut sudah terdaftar untuk user lain.');
                    }
                    mysqli_free_result($result_check_nisnip_edit);
                }

                $update_clauses = [];
                $changes_made = false;

                // --- Handle updates to 'users' table ---
                if ($edit_nama !== $current_nama_display) {
                    $update_clauses[] = "nama = '{$edit_nama}'";
                    $changes_made = true;
                }
                if ($edit_nisnip !== $current_nisnip_display) {
                    $update_clauses[] = "nis_nip = '{$edit_nisnip}'";
                    $changes_made = true;
                }
                if (!empty($edit_new_password_raw)) {
                    $hashed_password = password_hash($edit_new_password_raw, PASSWORD_DEFAULT);
                    $hashed_password_escaped = mysqli_real_escape_string($conn, $hashed_password);
                    $update_clauses[] = "password = '{$hashed_password_escaped}'";
                    $changes_made = true;
                }

                $new_foto_profile = $current_foto_profile_display;
                if (isset($_FILES['edit_foto_profile']) && $_FILES['edit_foto_profile']['error'] === UPLOAD_ERR_OK) {
                    $target_dir = $asset_base_path_users;
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    $file_extension = pathinfo($_FILES['edit_foto_profile']['name'], PATHINFO_EXTENSION);
                    $unique_file_name = uniqid('profile_') . '.' . $file_extension;
                    $target_file = $target_dir . $unique_file_name;
                    $imageFileType = strtolower($file_extension);

                    $check_img = getimagesize($_FILES['edit_foto_profile']['tmp_name']);
                    if ($check_img !== false && in_array($imageFileType, ["jpg", "png", "jpeg", "gif"]) && $_FILES['edit_foto_profile']['size'] <= 5000000) {
                        if (!move_uploaded_file($_FILES['edit_foto_profile']['tmp_name'], $target_file)) {
                            throw new Exception('Gagal mengunggah foto profil baru. Error code: ' . $_FILES['edit_foto_profile']['error']);
                        }
                        $new_foto_profile = mysqli_real_escape_string($conn, $unique_file_name);
                        $update_clauses[] = "foto_profile = '{$new_foto_profile}'";
                        $changes_made = true;

                        if ($current_foto_profile_display !== 'default.png' && !empty($current_foto_profile_display) && file_exists($asset_base_path_users . $current_foto_profile_display)) {
                            unlink($asset_base_path_users . $current_foto_profile_display);
                        }
                    } else {
                        throw new Exception('File foto baru tidak valid atau terlalu besar (maks 5MB, format JPG, JPEG, PNG, GIF).');
                    }
                }

                // --- Handle Role Change and Specific Role Data Update ---
                if ($edit_role !== $current_role_display) {
                    // 1. Delete from old role's specific table if applicable
                    if ($current_role_display === 'siswa') {
                        $sql_delete_old_role = "DELETE FROM siswa WHERE user_id = '{$edit_id}'";
                        if (!mysqli_query($conn, $sql_delete_old_role)) {
                            throw new Exception('Gagal menghapus data dari tabel siswa saat perubahan role: ' . mysqli_error($conn));
                        }
                    } elseif ($current_role_display === 'guru') {
                        $sql_delete_old_role = "DELETE FROM guru WHERE user_id = '{$edit_id}'";
                        if (!mysqli_query($conn, $sql_delete_old_role)) {
                            throw new Exception('Gagal menghapus data dari tabel guru saat perubahan role: ' . mysqli_error($conn));
                        }
                    }

                    // 2. Insert into new role's specific table if applicable
                    if ($edit_role === 'siswa') {
                        // Ensure jurusan_id and kelas_id are passed and not empty when changing role to siswa
                        if (empty($edit_jurusan_id) || empty($edit_kelas_id)) {
                            throw new Exception('Jurusan dan Kelas harus diisi jika role diubah menjadi Siswa.');
                        }
                        $sql_add_new_role = "INSERT INTO siswa (user_id, jurusan_id, kelas_id) VALUES ('{$edit_id}', '{$edit_jurusan_id}', '{$edit_kelas_id}')";
                        if (!mysqli_query($conn, $sql_add_new_role)) {
                            throw new Exception('Gagal menambahkan data ke tabel siswa setelah perubahan role: ' . mysqli_error($conn));
                        }
                    } elseif ($edit_role === 'guru') {
                        // Ensure jabatan_id is passed and not empty when changing role to guru
                        if (empty($edit_jabatan_id)) {
                            throw new Exception('Jabatan harus diisi jika role diubah menjadi Guru.');
                        }
                        $sql_add_new_role = "INSERT INTO guru (user_id, jabatan_id) VALUES ('{$edit_id}', '{$edit_jabatan_id}')";
                        if (!mysqli_query($conn, $sql_add_new_role)) {
                            throw new Exception('Gagal menambahkan data ke tabel guru setelah perubahan role: ' . mysqli_error($conn));
                        }
                    }
                    $update_clauses[] = "role = '{$edit_role}'";
                    $changes_made = true;
                }
                // If role remains 'guru', update jabatan_id if it changed
                elseif ($edit_role === 'guru' && ($edit_jabatan_id != $current_jabatan_id_display)) {
                    $sql_update_jabatan = "UPDATE guru SET jabatan_id = '{$edit_jabatan_id}' WHERE user_id = '{$edit_id}'";
                    if (!mysqli_query($conn, $sql_update_jabatan)) {
                        throw new Exception('Gagal mengubah jabatan di tabel guru: ' . mysqli_error($conn));
                    }
                    $changes_made = true;
                }
                // If role remains 'siswa', update jurusan_id and kelas_id if they changed
                elseif ($edit_role === 'siswa' && ($edit_jurusan_id != $current_jurusan_id_display || $edit_kelas_id != $current_kelas_id_display)) {
                    $sql_update_siswa = "UPDATE siswa SET jurusan_id = '{$edit_jurusan_id}', kelas_id = '{$edit_kelas_id}' WHERE user_id = '{$edit_id}'";
                    if (!mysqli_query($conn, $sql_update_siswa)) {
                        throw new Exception('Gagal mengubah jurusan/kelas di tabel siswa: ' . mysqli_error($conn));
                    }
                    $changes_made = true;
                }


                // Execute update for 'users' table if any changes were made
                if ($changes_made) {
                    $update_clauses[] = "updated_at = CURRENT_TIMESTAMP";
                    $sql_update_user = "UPDATE users SET " . implode(', ', $update_clauses) . " WHERE id = '{$edit_id}'";

                    if (!mysqli_query($conn, $sql_update_user)) {
                        throw new Exception('Gagal mengubah user di tabel users: ' . mysqli_error($conn));
                    }
                    $_SESSION['success_message'] = 'User berhasil diubah!';
                } else {
                    $_SESSION['error_message'] = 'Tidak ada perubahan terdeteksi untuk user ini.';
                }
                break;

            case 'delete':
                // --- PENTING: mysqli_real_escape_string() digunakan di sini ---
                $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id_user'] ?? '');
                if (empty($delete_id)) {
                    throw new Exception('ID User tidak boleh kosong untuk menghapus.');
                }

                // Fetch user's role and photo before deleting from users table
                $sql_fetch_user_details = "SELECT role, foto_profile FROM users WHERE id = '{$delete_id}'";
                $result_fetch_user_details = mysqli_query($conn, $sql_fetch_user_details);
                if (!$result_fetch_user_details) {
                    throw new Exception('Gagal mengambil detail user untuk dihapus: ' . mysqli_error($conn));
                }
                $user_details = mysqli_fetch_assoc($result_fetch_user_details);
                mysqli_free_result($result_fetch_user_details);

                $role_to_delete = $user_details['role'] ?? null;
                $foto_to_delete = $user_details['foto_profile'] ?? 'default.png';

                // Delete from child tables (siswa, guru) FIRST if ON DELETE CASCADE is NOT used
                if ($role_to_delete === 'siswa') {
                    $sql_delete_siswa = "DELETE FROM siswa WHERE user_id = '{$delete_id}'";
                    if (!mysqli_query($conn, $sql_delete_siswa)) {
                        throw new Exception('Gagal menghapus data dari tabel siswa: ' . mysqli_error($conn));
                    }
                } elseif ($role_to_delete === 'guru') {
                    $sql_delete_guru = "DELETE FROM guru WHERE user_id = '{$delete_id}'";
                    if (!mysqli_query($conn, $sql_delete_guru)) {
                        throw new Exception('Gagal menghapus data dari tabel guru: ' . mysqli_error($conn));
                    }
                }

                // Now delete from 'users' table
                $sql_delete_user = "DELETE FROM users WHERE id = '{$delete_id}'";
                if (!mysqli_query($conn, $sql_delete_user)) {
                    throw new Exception('Gagal menghapus user dari tabel users: ' . mysqli_error($conn));
                }

                $_SESSION['success_message'] = 'User berhasil dihapus!';

                // Delete the actual profile picture file if it's not the default
                if (!empty($foto_to_delete) && $foto_to_delete !== 'default.png' && file_exists($asset_base_path_users . $foto_to_delete)) {
                    unlink($asset_base_path_users . $foto_to_delete);
                }
                break;

            default:
                throw new Exception('Aksi tidak valid.');
        }
        mysqli_commit($conn); // Commit transaction if everything was successful
    } catch (Exception $e) {
        mysqli_rollback($conn); // Rollback on any exception
        $_SESSION['error_message'] = $e->getMessage();
        // If photo was uploaded during 'add' and transaction rolled back, delete the orphaned file.
        if ($action === 'add' && isset($foto_profile_name) && $foto_profile_name !== 'default.png' && file_exists($asset_base_path_users . $foto_profile_name)) {
            unlink($asset_base_path_users . $foto_profile_name);
        }
    }
}

// --- Fetch All Users Data for Display (Modified to include guru/siswa details) ---
$users_data = [];
$online_status_threshold_seconds = 5 * 60; // Define online status threshold (5 minutes)

if (empty($error_message) && isset($conn) && $conn->ping()) {
    // Modified query to also fetch jabatan_id for guru roles and jurusan_id/kelas_id for siswa roles
    $sql_fetch_users = "
        SELECT
            u.id, u.nis_nip, u.role, u.nama, u.foto_profile, u.created_at, u.updated_at, u.last_seen_at,
            g.jabatan_id,
            s.jurusan_id, s.kelas_id
        FROM users u
        LEFT JOIN guru g ON u.id = g.user_id AND u.role = 'guru'
        LEFT JOIN siswa s ON u.id = s.user_id AND u.role = 'siswa'
        ORDER BY u.nama ASC
    ";
    $result_fetch_users = mysqli_query($conn, $sql_fetch_users);

    if ($result_fetch_users) {
        while ($row = mysqli_fetch_assoc($result_fetch_users)) {
            $last_seen_timestamp = strtotime($row['last_seen_at'] ?? '');
            $current_timestamp = time();

            if ($last_seen_timestamp && ($current_timestamp - $last_seen_timestamp) < $online_status_threshold_seconds) {
                $row['is_online'] = true;
                $row['status_text'] = 'Online';
                $row['status_color_class'] = 'bg-green-500';
            } else {
                $row['is_online'] = false;
                $row['status_text'] = 'Offline';
                $row['status_color_class'] = 'bg-gray-400';
            }
            $users_data[] = $row;
        }
        mysqli_free_result($result_fetch_users);
    } else {
        $error_message = 'Gagal mengambil data user: ' . mysqli_error($conn);
    }
}
// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}