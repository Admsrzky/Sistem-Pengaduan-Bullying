<?php
// auth/authforgotpassword.php

// Pastikan file koneksi database sudah di-include di index.php
// yaitu $conn dari config/database.php sudah tersedia

if (isset($_POST['forgot-password-submit'])) {
    $nis_nip = trim($_POST['forgot_nis_nip']);

    if (empty($nis_nip)) {
        $_SESSION['forgot_password_status'] = 'empty';
        $_SESSION['forgot_password_message'] = 'NIS/NIP wajib diisi.';
        exit(); // Hentikan eksekusi lebih lanjut
    }

    // Ambil data user dari database
    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("SELECT id, email FROM users WHERE nis_nip = ?");
    if (!$stmt) {
        $_SESSION['forgot_password_status'] = 'error';
        $_SESSION['forgot_password_message'] = 'Terjadi kesalahan persiapan query: ' . $conn->error;
        exit();
    }
    $stmt->bind_param("s", $nis_nip);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user) {
        // User ditemukan, lanjutkan dengan proses reset kata sandi
        // 1. Hasilkan token unik dan aman.
        $token = bin2hex(random_bytes(32));
        // 2. Tetapkan waktu kedaluwarsa untuk token (misalnya, 1 jam dari sekarang).
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

        // 3. Simpan token dan waktu kedaluwarsanya di database untuk user tersebut.
        // Anda mungkin perlu menambahkan kolom 'reset_token' (VARCHAR) dan 'reset_expires_at' (DATETIME)
        // ke tabel 'users' Anda jika belum ada.
        $update_stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires_at = ? WHERE id = ?");
        if (!$update_stmt) {
            $_SESSION['forgot_password_status'] = 'error';
            $_SESSION['forgot_password_message'] = 'Terjadi kesalahan persiapan update query: ' . $conn->error;
            exit();
        }
        $update_stmt->bind_param("ssi", $token, $expires, $user['id']);
        $update_stmt->execute();
        $update_stmt->close();

        // 4. Kirim email ke user dengan tautan yang berisi token.
        // Contoh tautan: yourdomain.com/reset_password.php?token=$token
        // Bagian ini membutuhkan konfigurasi server email dan mungkin library seperti PHPMailer.
        // Untuk demo, kita hanya akan mengatur pesan sukses.
        /*
        // Contoh Penggunaan PHPMailer (Anda perlu menginstalnya via Composer atau download manual)
        // require 'path/to/PHPMailer/src/PHPMailer.php';
        // require 'path/to/PHPMailer/src/SMTP.php';
        // require 'path/to/PHPMailer/src/Exception.php';

        // use PHPMailer\PHPMailer\PHPMailer;
        // use PHPMailer\PHPMailer\SMTP;
        // use PHPMailer\PHPMailer\Exception;

        // $mail = new PHPMailer(true);
        // try {
        //     //Server settings
        //     $mail->isSMTP();
        //     $mail->Host       = 'smtp.example.com'; // Ganti dengan SMTP host Anda
        //     $mail->SMTPAuth   = true;
        //     $mail->Username   = 'your_email@example.com'; // Ganti dengan username email Anda
        //     $mail->Password   = 'your_email_password';    // Ganti dengan password email Anda
        //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Atau PHPMailer::ENCRYPTION_SMTPS
        //     $mail->Port       = 587; // Atau 465 untuk SMTPS

        //     //Recipients
        //     $mail->setFrom('no-reply@yourdomain.com', 'SIPENG MAN 1 CILEGON');
        //     $mail->addAddress($user['email'], $user['nama'] ?? 'User'); // Asumsi ada kolom 'email' dan 'nama'

        //     //Content
        //     $mail->isHTML(true);
        //     $mail->Subject = 'Reset Kata Sandi Anda';
        //     $mail->Body    = "Halo,<br><br>Anda meminta untuk mereset kata sandi Anda. Silakan klik link berikut untuk mereset kata sandi Anda: <a href='http://localhost/sipeng/reset_password.php?token=$token'>Reset Kata Sandi</a><br><br>Link ini akan kedaluwarsa dalam 1 jam.<br><br>Jika Anda tidak meminta ini, abaikan email ini.<br><br>Terima kasih,<br>SIPENG MAN 1 CILEGON";

        //     $mail->send();
        //     $_SESSION['forgot_password_status'] = 'success';
        //     $_SESSION['forgot_password_message'] = 'Link reset kata sandi telah dikirim ke email Anda.';
        // } catch (Exception $e) {
        //     $_SESSION['forgot_password_status'] = 'error';
        //     $_SESSION['forgot_password_message'] = 'Gagal mengirim email reset kata sandi. Silakan coba lagi. Mailer Error: ' . $mail->ErrorInfo;
        // }
        */

        // Untuk saat ini, tanpa pengiriman email yang sebenarnya:
        $_SESSION['forgot_password_status'] = 'success';
        $_SESSION['forgot_password_message'] = 'Jika NIS/NIP terdaftar, link reset kata sandi akan dikirim ke email yang terdaftar.';

        // Catat token (untuk pengujian, hapus di produksi)
        // error_log("Password reset token for NIS/NIP " . $nis_nip . ": " . $token);


    } else {
        // User tidak ditemukan
        $_SESSION['forgot_password_status'] = 'not_found';
        $_SESSION['forgot_password_message'] = 'NIS/NIP yang Anda masukkan tidak terdaftar.';
    }
    exit(); // Hentikan eksekusi lebih lanjut
}
