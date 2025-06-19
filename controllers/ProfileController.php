<?php

// Initialize variables for messages
$success_message = '';
$error_message = '';

// --- Handle messages after redirect ---
// Check for success message from session after a redirect
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']); // Clear the message after displaying
}

// Check for error message from session after a redirect
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']); // Clear the message after displaying
}


// --- Cek apakah NIS/NIP pengguna dari session sudah ada ---
if (is_null($user_id)) { // Menggunakan $user_id karena di header.php mungkin ini yang diset untuk NIS/NIP
    $_SESSION['error_message'] = "Sesi pengguna tidak ditemukan. Mohon login kembali.";
    header('Location: ../../login.php'); // Redirect to login
    exit();
}

// --- Handle Profile Update Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') { // Removed empty($error_message) from here as it's handled via session
    // Include your database connection file
    require_once '../../config/database.php'; // Ensure this path is correct relative to profile.php

    // Check if $conn is successfully established
    if (!isset($conn) || $conn->connect_error) {
        $_SESSION['error_message'] = "Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error');
        header('Location: profile_admin.php'); // Redirect to show error
        exit();
    } else {
        // Now it's safe to escape $user_id
        $nis_nip_for_query = mysqli_real_escape_string($conn, $user_id);

        // Escape all user inputs immediately
        $new_nama = mysqli_real_escape_string($conn, $_POST['nama'] ?? '');
        $current_password = mysqli_real_escape_string($conn, $_POST['current_password'] ?? '');
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password'] ?? '');
        $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password'] ?? '');

        $update_set_clauses = [];
        $changes_made = false;

        // Update Name
        if (!empty($new_nama) && $new_nama !== $user_nama) {
            $update_set_clauses[] = "nama = '$new_nama'";
            $changes_made = true;
        }

        // Update Password
        if (!empty($current_password) || !empty($new_password) || !empty($confirm_password)) {
            // First, retrieve the user's current hashed password from the database using nis_nip
            $sql_check_pw = "SELECT password FROM users WHERE id = '$nis_nip_for_query'"; // Use 'id' column as it holds nis_nip
            $result_check_pw = mysqli_query($conn, $sql_check_pw);

            if ($result_check_pw) {
                $user_db_data = mysqli_fetch_assoc($result_check_pw);
                // KRITIS: Gunakan password_verify() untuk cek password saat ini
                if ($user_db_data && password_verify($current_password, $user_db_data['password'])) {
                    // Current password matches
                    if (!empty($new_password)) {
                        if ($new_password === $confirm_password) {
                            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                            $hashed_password_escaped = mysqli_real_escape_string($conn, $hashed_password);
                            $update_set_clauses[] = "password = '$hashed_password_escaped'";
                            $changes_made = true;
                        } else {
                            $_SESSION['error_message'] = 'New password and confirm password do not match.';
                            // Don't set $error_message here, it will be pulled from session after redirect
                        }
                    } else {
                        // If current password was provided but new password is empty, and no other changes
                        if (empty($new_nama) && empty($_FILES['foto_profile']['name'] ?? '')) {
                            $_SESSION['error_message'] = 'New password cannot be empty if you provided current password.';
                        }
                    }
                } else {
                    $_SESSION['error_message'] = 'Current password is incorrect. No password changes applied.';
                }
            } else {
                $_SESSION['error_message'] = 'Error checking current password: ' . mysqli_error($conn);
            }
        }

        // Handle Profile Picture Upload
        if (isset($_FILES['foto_profile']) && $_FILES['foto_profile']['error'] === UPLOAD_ERR_OK) {
            $target_dir = '../../assets/img/profile/';
            // Ensure the directory exists and is writable
            if (!is_dir($target_dir)) {
                if (!mkdir($target_dir, 0755, true)) {
                    $_SESSION['error_message'] = 'Failed to create upload directory.';
                }
            }

            if (!isset($_SESSION['error_message'])) { // Only proceed if no file-related error yet
                $file_extension = pathinfo($_FILES['foto_profile']['name'], PATHINFO_EXTENSION);
                $new_file_name = uniqid('profile_') . '.' . $file_extension;
                $target_file = $target_dir . $new_file_name;
                $imageFileType = strtolower($file_extension);

                // Check if image file is a actual image or fake image
                $check = getimagesize($_FILES['foto_profile']['tmp_name']);
                if ($check !== false) {
                    // Allow certain file formats
                    if (!in_array($imageFileType, ["jpg", "png", "jpeg", "gif"])) {
                        $_SESSION['error_message'] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed for profile picture.";
                    } else {
                        // Check file size (e.g., 5MB limit)
                        if ($_FILES['foto_profile']['size'] > 5000000) {
                            $_SESSION['error_message'] = "Sorry, your file is too large (max 5MB).";
                        } else {
                            if (move_uploaded_file($_FILES['foto_profile']['tmp_name'], $target_file)) {
                                // Delete old profile picture if it's not the default and exists
                                $current_profile_path = '../../assets/img/profile/' . $user_foto_profile;
                                if ($user_foto_profile !== 'default.png' && file_exists($current_profile_path)) {
                                    unlink($current_profile_path);
                                }
                                $new_file_name_escaped = mysqli_real_escape_string($conn, $new_file_name);
                                $update_set_clauses[] = "foto_profile = '$new_file_name_escaped'";
                                $changes_made = true;
                            } else {
                                $_SESSION['error_message'] = 'Sorry, there was an error uploading your file. Error code: ' . $_FILES['foto_profile']['error'];
                            }
                        }
                    }
                } else {
                    $_SESSION['error_message'] = 'File is not an image or corrupted.';
                }
            }
        }

        // Only proceed to update if no errors have been set in session so far
        if (!isset($_SESSION['error_message'])) {
            // Add updated_at field if any changes were made
            if ($changes_made) {
                $update_set_clauses[] = "updated_at = CURRENT_TIMESTAMP";
            }

            // Execute the update if there are fields to update
            if (!empty($update_set_clauses)) {
                // Construct the SQL query - using nis_nip from database
                // IMPORTANT: Using 'id' column from the database as it corresponds to nis_nip
                $sql = "UPDATE users SET " . implode(', ', $update_set_clauses) . " WHERE nis_nip = '$nis_nip_for_query'";

                if (mysqli_query($conn, $sql)) {
                    $_SESSION['success_message'] = 'Profile updated successfully!';
                    // Update session variables for immediate display in sidebar/next page load
                    if (!empty($new_nama) && $new_nama !== $user_nama) {
                        $_SESSION['nama'] = $new_nama;
                    }
                    if (isset($new_file_name)) {
                        $_SESSION['foto_profile'] = $new_file_name;
                    }
                } else {
                    $_SESSION['error_message'] = 'Error updating profile: ' . mysqli_error($conn);
                }
            } elseif (!$changes_made) {
                $_SESSION['error_message'] = 'No changes detected. Please modify at least one field to save.';
            }
        }
        mysqli_close($conn);

        // // Redirect after POST to prevent form resubmission and ensure session updates are picked up
        // header('Location: profile_admin.php');
        // exit();
    }
}