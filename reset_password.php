<?php
session_start();
include 'config/database.php'; // Menggunakan koneksi MySQLi

$token = $_GET['token'] ?? '';

if (empty($token)) {
    $_SESSION['error_message'] = 'Token reset tidak valid.';
    header('Location: index.php');
    exit();
}

// Cari user berdasarkan token
$stmt = $conn->prepare("SELECT id, nis_nip, reset_expires_at FROM users WHERE reset_token = ?");
if (!$stmt) {
    $_SESSION['error_message'] = 'Terjadi kesalahan query: ' . $conn->error;
    header('Location: index.php');
    exit();
}
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || strtotime($user['reset_expires_at']) < time()) {
    $_SESSION['error_message'] = 'Token reset tidak valid atau sudah kedaluwarsa.';
    header('Location: index.php');
    exit();
}

// Jika token valid, tampilkan form reset password
$message = '';
if (isset($_POST['set_new_password'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $message = 'Kata sandi baru dan konfirmasi wajib diisi.';
    } elseif ($new_password !== $confirm_password) {
        $message = 'Konfirmasi kata sandi tidak cocok.';
    } elseif (strlen($new_password) < 8) {
        $message = 'Kata sandi minimal 8 karakter.';
    } elseif (!preg_match('/[A-Z]/', $new_password) || !preg_match('/[0-9]/', $new_password)) {
        $message = 'Kata sandi harus mengandung setidaknya satu huruf besar dan satu angka.';
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update password dan kosongkan token reset
        $update_stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires_at = NULL WHERE id = ?");
        if (!$update_stmt) {
            $message = 'Terjadi kesalahan update query: ' . $conn->error;
        } else {
            $update_stmt->bind_param("si", $hashed_password, $user['id']);
            if ($update_stmt->execute()) {
                $_SESSION['success_message'] = 'Kata sandi Anda berhasil direset. Silakan login dengan kata sandi baru Anda.';
                header('Location: index.php');
                exit();
            } else {
                $message = 'Gagal mereset kata sandi: ' . $conn->error;
            }
            $update_stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Kata Sandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-gradient-to-br from-teal-50 to-white flex items-center justify-center min-h-screen p-6">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md border border-teal-200 p-8">
        <h2 class="text-2xl font-bold text-center text-teal-700 mb-6">Reset Kata Sandi</h2>
        <p class="text-gray-600 text-center mb-6">Masukkan kata sandi baru Anda untuk NIS/NIP:
            <?php echo htmlspecialchars($user['nis_nip']); ?></p>

        <?php if (!empty($message)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline"><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label for="new_password" class="block mb-2 font-semibold text-gray-700">Kata Sandi Baru</label>
                <input type="password" id="new_password" name="new_password" required
                    class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
            </div>
            <div>
                <label for="confirm_password" class="block mb-2 font-semibold text-gray-700">Konfirmasi Kata Sandi
                    Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                    class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
            </div>
            <button type="submit" name="set_new_password"
                class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Setel
                Kata Sandi Baru</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['success_message'])): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '<?php echo htmlspecialchars($_SESSION['success_message']); ?>',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'index.php'; // Redirect ke halaman login
                });
                <?php unset($_SESSION['success_message']); ?>
            <?php elseif (isset($_SESSION['error_message'])): ?>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: '<?php echo htmlspecialchars($_SESSION['error_message']); ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['error_message']); ?>
            <?php endif; ?>
        });
    </script>
</body>

</html>