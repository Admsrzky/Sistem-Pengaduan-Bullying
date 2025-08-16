<?php
include '../../config/database.php';

/**
 * =================================================================
 * UsersController.php
 * (Menggunakan Notifikasi via URL & Pola PRG)
 * =================================================================
 */

// Asumsikan koneksi '$conn' sudah tersedia dari file yang meng-include controller ini.
$asset_base_path_users = '../../assets/img/profile/';

// --- Fetch Master Data for dropdowns ---
$jabatan_options = [];
$jurusan_options = [];
$kelas_options = [];
$fetch_master_error = '';

if (isset($conn) && $conn->ping()) {
    $result_jabatan = mysqli_query($conn, "SELECT id, nama_jabatan FROM jabatan_guru ORDER BY nama_jabatan ASC");
    if ($result_jabatan) $jabatan_options = mysqli_fetch_all($result_jabatan, MYSQLI_ASSOC);
    else $fetch_master_error = "Gagal mengambil data jabatan.";

    $result_jurusan = mysqli_query($conn, "SELECT id, nama_jurusan FROM jurusan ORDER BY nama_jurusan ASC");
    if ($result_jurusan) $jurusan_options = mysqli_fetch_all($result_jurusan, MYSQLI_ASSOC);
    else $fetch_master_error = "Gagal mengambil data jurusan.";

    $result_kelas = mysqli_query($conn, "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC");
    if ($result_kelas) $kelas_options = mysqli_fetch_all($result_kelas, MYSQLI_ASSOC);
    else $fetch_master_error = "Gagal mengambil data kelas.";
} else {
    $fetch_master_error = "Koneksi database gagal.";
}

// --- Handle Form Submissions (Add/Edit/Delete) ---
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
            case 'add':
                // Logika 'add' Anda sudah bagus dan lengkap
                $new_nisnip = mysqli_real_escape_string($conn, $_POST['nis_nip'] ?? '');
                $new_nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
                $new_password_raw = $_POST['password'] ?? '';
                $new_role = mysqli_real_escape_string($conn, $_POST['role'] ?? '');
                $new_jurusan_id = mysqli_real_escape_string($conn, $_POST['add_jurusan_id'] ?? '');
                $new_kelas_id = mysqli_real_escape_string($conn, $_POST['add_kelas_id'] ?? '');
                $new_jabatan_id = mysqli_real_escape_string($conn, $_POST['add_jabatan_id'] ?? '');

                if (empty($new_nisnip) || empty($new_nama) || empty($new_password_raw) || empty($new_role)) {
                    throw new Exception('Semua field wajib diisi.');
                }
                if ($new_role === 'siswa' && (empty($new_jurusan_id) || empty($new_kelas_id))) {
                    throw new Exception('Jurusan dan Kelas wajib diisi untuk siswa.');
                }
                if ($new_role === 'guru' && empty($new_jabatan_id)) {
                    throw new Exception('Jabatan wajib diisi untuk guru.');
                }

                $check_nisnip_sql = "SELECT nis_nip FROM users WHERE nis_nip = '{$new_nisnip}'";
                $check_result = mysqli_query($conn, $check_nisnip_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    throw new Exception('NIS/NIP tersebut sudah terdaftar.');
                }

                $hashed_password = password_hash($new_password_raw, PASSWORD_DEFAULT);
                $foto_profile_name = 'default.png';

                if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
                    $unique_file_name = uniqid('profile_') . '.' . pathinfo($_FILES['foto_profile']['name'], PATHINFO_EXTENSION);
                    if (!move_uploaded_file($_FILES['foto_profile']['tmp_name'], $asset_base_path_users . $unique_file_name)) {
                        throw new Exception('Gagal mengunggah foto profil.');
                    }
                    $foto_profile_name = $unique_file_name;
                }

                $sql_add_user = "INSERT INTO users (nis_nip, password, role, nama, foto_profile, created_at, updated_at) VALUES ('{$new_nisnip}', '{$hashed_password}', '{$new_role}', '{$new_nama}', '{$foto_profile_name}', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
                if (!mysqli_query($conn, $sql_add_user)) {
                    throw new Exception('Gagal menambahkan user: ' . mysqli_error($conn));
                }
                $last_user_id = mysqli_insert_id($conn);

                if ($new_role === 'siswa') {
                    $sql_add_siswa = "INSERT INTO siswa (user_id, jurusan_id, kelas_id) VALUES ('{$last_user_id}', '{$new_jurusan_id}', '{$new_kelas_id}')";
                    if (!mysqli_query($conn, $sql_add_siswa)) throw new Exception('Gagal menambahkan data siswa.');
                } elseif ($new_role === 'guru') {
                    $sql_add_guru = "INSERT INTO guru (user_id, jabatan_id) VALUES ('{$last_user_id}', '{$new_jabatan_id}')";
                    if (!mysqli_query($conn, $sql_add_guru)) throw new Exception('Gagal menambahkan data guru.');
                }

                $status = 'success';
                $msg = 'User ' . htmlspecialchars($new_nama) . ' berhasil ditambahkan!';
                break;

            case 'edit':
                $edit_id = mysqli_real_escape_string($conn, $_POST['edit_id_user'] ?? '');
                $edit_nama = mysqli_real_escape_string($conn, $_POST['edit_nama'] ?? '');
                $edit_role = mysqli_real_escape_string($conn, $_POST['edit_role'] ?? '');
                $edit_new_password_raw = $_POST['edit_new_password'] ?? '';
                $edit_nisnip = mysqli_real_escape_string($conn, $_POST['edit_nisnip'] ?? '');
                $edit_jabatan_id = mysqli_real_escape_string($conn, $_POST['edit_jabatan_id'] ?? '');
                $edit_jurusan_id = mysqli_real_escape_string($conn, $_POST['edit_jurusan_id'] ?? '');
                $edit_kelas_id = mysqli_real_escape_string($conn, $_POST['edit_kelas_id'] ?? '');

                $current_nama_display = mysqli_real_escape_string($conn, $_POST['current_nama_display'] ?? '');
                $current_role_display = mysqli_real_escape_string($conn, $_POST['current_role_display'] ?? '');
                $current_foto_profile_display = mysqli_real_escape_string($conn, $_POST['current_foto_profile_display'] ?? 'default.png');
                $current_nisnip_display = mysqli_real_escape_string($conn, $_POST['current_nisnip_display'] ?? '');
                $current_jabatan_id_display = mysqli_real_escape_string($conn, $_POST['current_jabatan_id_display'] ?? '');
                $current_jurusan_id_display = mysqli_real_escape_string($conn, $_POST['current_jurusan_id_display'] ?? '');
                $current_kelas_id_display = mysqli_real_escape_string($conn, $_POST['current_kelas_id_display'] ?? '');

                if (empty($edit_id) || empty($edit_nama) || empty($edit_role) || empty($edit_nisnip)) {
                    throw new Exception('NIS/NIP, Nama, dan Role tidak boleh kosong.');
                }
                if ($edit_role === 'guru' && empty($edit_jabatan_id)) {
                    throw new Exception('Jabatan harus diisi jika role adalah Guru.');
                }
                if ($edit_role === 'siswa' && (empty($edit_jurusan_id) || empty($edit_kelas_id))) {
                    throw new Exception('Jurusan dan Kelas harus diisi jika role adalah Siswa.');
                }

                if ($edit_nisnip !== $current_nisnip_display) {
                    $sql_check_nisnip_edit = "SELECT id FROM users WHERE nis_nip = '{$edit_nisnip}' AND id != '{$edit_id}'";
                    $result_check_nisnip_edit = mysqli_query($conn, $sql_check_nisnip_edit);
                    if (mysqli_num_rows($result_check_nisnip_edit) > 0) {
                        throw new Exception('NIS/NIP baru sudah terdaftar untuk user lain.');
                    }
                }

                $update_clauses = [];
                $changes_made = false;

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
                    $update_clauses[] = "password = '{$hashed_password}'";
                    $changes_made = true;
                }

                if (isset($_FILES['edit_foto_profile']) && $_FILES['edit_foto_profile']['error'] === UPLOAD_ERR_OK) {
                    $unique_file_name = uniqid('profile_') . '.' . pathinfo($_FILES['edit_foto_profile']['name'], PATHINFO_EXTENSION);
                    if (!move_uploaded_file($_FILES['edit_foto_profile']['tmp_name'], $asset_base_path_users . $unique_file_name)) {
                        throw new Exception('Gagal mengunggah foto profil baru.');
                    }
                    $update_clauses[] = "foto_profile = '{$unique_file_name}'";
                    $changes_made = true;
                    if ($current_foto_profile_display !== 'default.png' && file_exists($asset_base_path_users . $current_foto_profile_display)) {
                        unlink($asset_base_path_users . $current_foto_profile_display);
                    }
                }

                if ($edit_role !== $current_role_display) {
                    if ($current_role_display === 'siswa') mysqli_query($conn, "DELETE FROM siswa WHERE user_id = '{$edit_id}'");
                    elseif ($current_role_display === 'guru') mysqli_query($conn, "DELETE FROM guru WHERE user_id = '{$edit_id}'");

                    if ($edit_role === 'siswa') {
                        $sql_add_new_role = "INSERT INTO siswa (user_id, jurusan_id, kelas_id) VALUES ('{$edit_id}', '{$edit_jurusan_id}', '{$edit_kelas_id}')";
                        if (!mysqli_query($conn, $sql_add_new_role)) throw new Exception('Gagal menambah data ke tabel siswa.');
                    } elseif ($edit_role === 'guru') {
                        $sql_add_new_role = "INSERT INTO guru (user_id, jabatan_id) VALUES ('{$edit_id}', '{$edit_jabatan_id}')";
                        if (!mysqli_query($conn, $sql_add_new_role)) throw new Exception('Gagal menambah data ke tabel guru.');
                    }
                    $update_clauses[] = "role = '{$edit_role}'";
                    $changes_made = true;
                } else {
                    if ($edit_role === 'guru' && ($edit_jabatan_id != $current_jabatan_id_display)) {
                        $sql_update_jabatan = "UPDATE guru SET jabatan_id = '{$edit_jabatan_id}' WHERE user_id = '{$edit_id}'";
                        if (!mysqli_query($conn, $sql_update_jabatan)) throw new Exception('Gagal mengubah jabatan guru.');
                        $changes_made = true;
                    }
                    if ($edit_role === 'siswa' && ($edit_jurusan_id != $current_jurusan_id_display || $edit_kelas_id != $current_kelas_id_display)) {
                        $sql_update_siswa = "UPDATE siswa SET jurusan_id = '{$edit_jurusan_id}', kelas_id = '{$edit_kelas_id}' WHERE user_id = '{$edit_id}'";
                        if (!mysqli_query($conn, $sql_update_siswa)) throw new Exception('Gagal mengubah data siswa.');
                        $changes_made = true;
                    }
                }

                if ($changes_made) {
                    $update_clauses[] = "updated_at = CURRENT_TIMESTAMP";
                    $sql_update_user = "UPDATE users SET " . implode(', ', $update_clauses) . " WHERE id = '{$edit_id}'";
                    if (!mysqli_query($conn, $sql_update_user)) {
                        throw new Exception('Gagal mengubah data user utama: ' . mysqli_error($conn));
                    }
                    $status = 'success';
                    $msg = 'User berhasil diubah!';
                } else {
                    $status = 'info';
                    $msg = 'Tidak ada perubahan terdeteksi.';
                }
                break;

            case 'delete':
                $delete_id = mysqli_real_escape_string($conn, $_POST['delete_id_user'] ?? '');
                if (empty($delete_id)) {
                    throw new Exception('ID User tidak valid untuk dihapus.');
                }

                $sql_get_details = "SELECT role, foto_profile FROM users WHERE id = '{$delete_id}'";
                $result_details = mysqli_query($conn, $sql_get_details);
                if (mysqli_num_rows($result_details) === 0) {
                    throw new Exception('User yang akan dihapus tidak ditemukan.');
                }
                $user_details = mysqli_fetch_assoc($result_details);
                $role_to_delete = $user_details['role'];
                $foto_to_delete = $user_details['foto_profile'];

                if ($role_to_delete === 'siswa') {
                    if (!mysqli_query($conn, "DELETE FROM siswa WHERE user_id = '{$delete_id}'")) throw new Exception('Gagal menghapus data dari tabel siswa.');
                } elseif ($role_to_delete === 'guru') {
                    if (!mysqli_query($conn, "DELETE FROM guru WHERE user_id = '{$delete_id}'")) throw new Exception('Gagal menghapus data dari tabel guru.');
                }

                if (!mysqli_query($conn, "DELETE FROM users WHERE id = '{$delete_id}'")) {
                    throw new Exception('Gagal menghapus user utama.');
                }

                if ($foto_to_delete !== 'default.png' && file_exists($asset_base_path_users . $foto_to_delete)) {
                    unlink($asset_base_path_users . $foto_to_delete);
                }

                $status = 'success';
                $msg = 'User berhasil dihapus!';
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

// --- Fetch All Users Data for Display ---
$users_data = [];
$fetch_error = '';

if (empty($fetch_master_error) && isset($conn) && $conn->ping()) {
    $sql_fetch_users = "
        SELECT u.id, u.nis_nip, u.role, u.nama, u.foto_profile, u.last_seen_at,
               g.jabatan_id, s.jurusan_id, s.kelas_id
        FROM users u
        LEFT JOIN guru g ON u.id = g.user_id AND u.role = 'guru'
        LEFT JOIN siswa s ON u.id = s.user_id AND u.role = 'siswa'
        ORDER BY u.nama ASC
    ";
    $result_fetch_users = mysqli_query($conn, $sql_fetch_users);

    if ($result_fetch_users) {
        $online_threshold = 5 * 60; // 5 menit
        while ($row = mysqli_fetch_assoc($result_fetch_users)) {
            $last_seen = strtotime($row['last_seen_at'] ?? '');
            $row['is_online'] = ($last_seen && (time() - $last_seen) < $online_threshold);
            $users_data[] = $row;
        }
    } else {
        $fetch_error = 'Gagal mengambil data user: ' . mysqli_error($conn);
    }
} else {
    $fetch_error = $fetch_master_error ?: "Koneksi database tidak tersedia.";
}
