<?php
// Inisialisasi variabel untuk pesan (local variables for display)
$success_message = '';
$error_message = '';

// --- Tangani pesan setelah redirect ---
// Cek pesan sukses dari session setelah redirect
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
}

// Cek pesan error dari session setelah redirect
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
}

// Define the asset base path for images displayed on the page
$asset_base_path = '../../assets/img/profile/'; // Path relative from views/admin/ to assets/img/profile/

// --- Cek apakah NIS/NIP pengguna dari session sudah ada ---
// Asumsi $_SESSION['nis_nip'] adalah identifikasi unik pengguna yang valid
if (!isset($_SESSION['nis_nip']) || is_null($_SESSION['nis_nip'])) {
    $_SESSION['error_message'] = "Sesi pengguna tidak ditemukan. Mohon login kembali.";
    header('Location: ../../login.php'); // Redirect ke halaman login (assuming login.php is in the project root)
    exit();
}
$user_id = $_SESSION['nis_nip']; // Ambil user_id dari session (NIS/NIP)

// Get current user data from session for displaying in the form
$user_nama = $_SESSION['nama'] ?? '';
$user_foto_profile = $_SESSION['foto_profile'] ?? 'default.png';

// --- Tangani Pengiriman Pembaruan Profil ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sertakan file koneksi database Anda
    // Path is relative from views/admin/ to config/database.php
    require_once '../../config/database.php';

    // Check if $conn is successfully connected
    if (!isset($conn) || $conn->connect_error) {
        $_SESSION['error_message'] = "Koneksi database gagal: " . ($conn->connect_error ?? 'Kesalahan tidak diketahui');
        header('Location: profile_admin.php'); // Redirect to display error
        exit();
    } else {
        // Secure $user_id (NIS/NIP) from SQL injection
        $nis_nip_for_query = mysqli_real_escape_string($conn, $user_id);

        // Secure all user inputs immediately
        $new_nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
        $current_password = mysqli_real_escape_string($conn, $_POST['current_password'] ?? '');
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password'] ?? '');
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password'] ?? '');

        $update_set_clauses = [];
        $changes_made = false;
        $password_updated = false; // Specific flag for password update

        // Update Name
        if (!empty($new_nama) && $new_nama !== $user_nama_current) {
            $update_set_clauses[] = "nama = '$new_nama'";
            $changes_made = true;
        }

        // --- Password Update Logic ---
        // Only process password update if any password input is provided
        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            // First, retrieve the user's current hashed password from the database using 'nis_nip'
            // THIS IS THE FIRST IMPORTANT CHANGE: from WHERE id to WHERE nis_nip
            $sql_check_pw = "SELECT password FROM users WHERE nis_nip = '$nis_nip_for_query'";
            $result_check_pw = mysqli_query($conn, $sql_check_pw);

            if ($result_check_pw && mysqli_num_rows($result_check_pw) > 0) {
                $user_db_data = mysqli_fetch_assoc($result_check_pw);

                // Verify current password
                if (password_verify($current_password, $user_db_data['password'])) {
                    // Current password matches
                    if (!empty($new_password)) {
                        if ($new_password === $confirm_password) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $hashed_password_escaped = mysqli_real_escape_string($conn, $hashed_password);
                            $update_set_clauses[] = "password = '$hashed_password_escaped'";
                            $changes_made = true;
                            $password_updated = true; // Set flag
                        } else {
                            // Error message set to $_SESSION['error_message'] to display after redirect
                            $_SESSION['error_message'] = 'Kata sandi baru dan konfirmasi kata sandi tidak cocok.';
                        }
                    } else {
                        // If current password is provided, but new password is empty
                        $_SESSION['error_message'] = 'Kata sandi baru tidak boleh kosong jika Anda ingin mengubah password.';
                    }
                } else {
                    // Current password is wrong
                    $_SESSION['error_message'] = 'Kata sandi saat ini salah. Tidak ada perubahan kata sandi diterapkan.';
                }
            } else {
                // User not found or query error when checking password
                $_SESSION['error_message'] = 'Kesalahan saat memeriksa kata sandi saat ini atau pengguna tidak ditemukan: ' .
                    mysqli_error($conn);
            }
        }
        // --- End Password Update Logic ---

        // Handle Profile Picture Upload
        if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
            $target_dir = '../../assets/img/profile/'; // Path relative from views/admin/
            // Ensure directory exists and is writable
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    $_SESSION['error_message'] = 'Gagal membuat direktori unggahan.'; // Set to $_SESSION['error_message']
                }
            }

            // Continue only if there's no directory or file-related error from previous checks
            if (empty($_SESSION['error_message'])) {
                $file_extension = pathinfo($_FILES['foto_profile']['name'], PATHINFO_EXTENSION);
                $new_file_name = uniqid('profile_') . '.' . $file_extension;
                $target_file = $target_dir . $new_file_name;
                $imageFileType = strtolower($file_extension);

                // Check if image file is an actual image or fake image
                $check = getimagesize($_FILES['foto_profile']['tmp_name']);
                if ($check !== false) {
                    // Allow certain file formats
                    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                        $_SESSION['error_message'] = "Maaf, hanya file JPG, JPEG, PNG & GIF yang diizinkan untuk foto profil.";
                    } else {
                        // Check file size (e.g., 5MB limit)
                        if ($_FILES['foto_profile']['size'] > 5000000) {
                            $_SESSION['error_message'] = "Maaf, file Anda terlalu besar (maksimal 5MB).";
                        } else {
                            if (move_uploaded_file($_FILES['foto_profile']['tmp_name'], $target_file)) {
                                // Delete old profile photo if it's not the default and exists
                                $current_profile_path = '../../assets/img/profile/' . $user_foto_profile_current;
                                if ($user_foto_profile_current !== 'default.png' && file_exists($current_profile_path)) {
                                    unlink($current_profile_path);
                                }
                                $new_file_name_escaped = mysqli_real_escape_string($conn, $new_file_name);
                                $update_set_clauses[] = "foto_profile = '$new_file_name_escaped'";
                                $changes_made = true;
                            } else {
                                $_SESSION['error_message'] = 'Maaf, terjadi kesalahan saat mengunggah file Anda. Kode kesalahan: ' .
                                    $_FILES['foto_profile']['error'];
                            }
                        }
                    }
                } else {
                    $_SESSION['error_message'] = 'File bukan gambar atau rusak.';
                }
            }
        }

        // --- SQL Update Execution Section ---
        // If no error has been set to the session from input validation or upload
        if (empty($_SESSION['error_message'])) {
            // Add updated_at column if any changes were made
            if ($changes_made) {
                $update_set_clauses[] = "updated_at = CURRENT_TIMESTAMP";
            }

            // Execute the update if there are columns to be updated
            if (!empty($update_set_clauses)) {
                // THIS IS THE SECOND IMPORTANT CHANGE: from WHERE id to WHERE nis_nip
                $sql = "UPDATE users SET " . implode(', ', $update_set_clauses) . " WHERE nis_nip = '$nis_nip_for_query'";

                if (mysqli_query($conn, $sql)) {
                    // Only set success message if no prior errors
                    $_SESSION['success_message'] = 'Profil berhasil diperbarui!';
                    if ($password_updated) { // Add info if password was also updated
                        $_SESSION['success_message'] .= ' Kata sandi juga telah diubah.';
                    }

                    // Update session variables for immediate display (e.g., in sidebar)
                    if (isset($new_nama) && $new_nama !== $user_nama_current) {
                        $_SESSION['nama'] = $new_nama;
                    }
                    if (isset($new_file_name)) {
                        $_SESSION['foto_profile'] = $new_file_name;
                    }
                } else {
                    // If there's an SQL error, set error message to session
                    $_SESSION['error_message'] = 'Kesalahan saat memperbarui profil: ' . mysqli_error($conn);
                }
            } elseif (!$changes_made && empty($password_updated)) {
                // If no changes were detected at all (name, photo, AND password not updated), set error message
                $_SESSION['error_message'] = 'Tidak ada perubahan terdeteksi. Harap ubah setidaknya satu bidang untuk menyimpan.';
            }
        }

        mysqli_close($conn);

        // Redirect after POST to prevent form resubmission on refresh
        header('Location: profile_admin.php');
        exit();
    }
}
