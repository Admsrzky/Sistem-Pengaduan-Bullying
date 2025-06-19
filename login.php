<?php
session_start(); // Pastikan session_start() ada di sini atau di file include awal
include 'config/database.php';

// Include PHP logic for authentication. These files should NOT contain <script> tags for SweetAlert.
include './auth/authlogin.php';
include './auth/authregister.php';

// Ini adalah status login yang diatur oleh authlogin.php
// Pastikan variabel $loginStatus diinisialisasi di authlogin.php
// Variabel $_SESSION['error_message'] juga diatur di authlogin.php
?>

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login & Register - SIPENG MAN 1 CILEGON</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    .tab-active {
        color: #0d9488;
        border-color: #0d9488;
        transition: color 0.3s, border-color 0.3s;
    }

    .tab-inactive {
        color: #4b5563;
        border-color: transparent;
        transition: color 0.3s, border-color 0.3s;
    }
    </style>
</head>

<body class="bg-gradient-to-br from-teal-50 to-white flex items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md text-center mb-8">
        <img src="assets/img/logo.png" alt="Logo SIPENG" class="mx-auto h-24 w-auto mb-4" />
        <h1 class="text-3xl font-extrabold text-teal-700 drop-shadow-md">SIPENG MAN 1 CILEGON</h1>
        <p class="text-teal-600 mt-2 font-medium">Silahkan login dan register untuk melanjutkan</p>
    </div>

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md border border-teal-200">
        <div class="flex border-b border-teal-200">
            <button id="login-tab"
                class="flex-1 py-4 font-semibold tab-active border-b-4 focus:outline-none">Login</button>
            <button id="register-tab"
                class="flex-1 py-4 font-semibold tab-inactive border-b-4 focus:outline-none">Register</button>
        </div>

        <div class="p-8">
            <form id="login-form" class="space-y-6" action="" method="POST">
                <div>
                    <label for="login-nisnip" class="block mb-2 font-semibold text-gray-700">NIS/NIP</label>
                    <input type="text" id="login-nisnip" name="nis_nip" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
                </div>
                <div>
                    <label for="login-password" class="block mb-2 font-semibold text-gray-700">Password</label>
                    <input type="password" id="login-password" name="password" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
                </div>
                <button type="submit" name="login-validate"
                    class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Login</button>
                <p class="text-center text-gray-600 text-sm mt-4">
                    Belum punya akun? <a href="#" id="to-register"
                        class="text-teal-600 font-semibold hover:underline">Daftar di sini</a>
                </p>
            </form>

            <form id="register-form" class="space-y-6 hidden" action="" method="POST">

                <?php
                // Tampilkan pesan register jika ada
                if (isset($_SESSION['register_status'])):
                    $regStatus = $_SESSION['register_status'];
                    $regMessage = '';
                    $bgColor = '';
                    $textColor = '';

                    switch ($regStatus) {
                        case 'success':
                            $regMessage = 'Akun berhasil dibuat! Silakan login.';
                            $bgColor = 'bg-green-100';
                            $textColor = 'text-green-700';
                            break;
                        case 'exists':
                            $regMessage = 'NIS/NIP sudah terdaftar. Gunakan NIS/NIP lain.';
                            $bgColor = 'bg-yellow-100';
                            $textColor = 'text-yellow-700';
                            break;
                        case 'mismatch':
                            $regMessage = 'Konfirmasi password tidak sesuai.';
                            $bgColor = 'bg-red-100';
                            $textColor = 'text-red-700';
                            break;
                        case 'empty':
                            $regMessage = 'Semua kolom wajib diisi.';
                            $bgColor = 'bg-red-100';
                            $textColor = 'text-red-700';
                            break;
                        case 'password_too_short': // Ditambahkan dari authregister.php
                            $regMessage = 'Kata sandi terlalu pendek. Minimal 8 karakter.';
                            $bgColor = 'bg-red-100';
                            $textColor = 'text-red-700';
                            break;
                        default:
                            $regMessage = $_SESSION['error_message_register'] ?? 'Terjadi kesalahan saat mendaftarkan akun.';
                            $bgColor = 'bg-red-100';
                            $textColor = 'text-red-700';
                    }
                ?>
                <div class="<?= $bgColor ?> border border-<?= substr($bgColor, 3, 4) ?>-400 <?= $textColor ?> px-4 py-3 rounded relative text-left"
                    role="alert">
                    <?= htmlspecialchars($regMessage) ?>
                </div>
                <?php endif; ?>

                <div>
                    <label for="register-nama" class="block mb-2 font-semibold text-gray-700">Nama Lengkap</label>
                    <input type="text" id="register-nama" name="nama" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
                </div>
                <div>
                    <label for="register-nisnip" class="block mb-2 font-semibold text-gray-700">NIS/NIP</label>
                    <input type="text" id="register-nisnip" name="nis_nip" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
                </div>
                <div>
                    <label for="register-password" class="block mb-2 font-semibold text-gray-700">Password</label>
                    <input type="password" id="register-password" name="password" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
                </div>
                <div>
                    <label for="register-password-confirm" class="block mb-2 font-semibold text-gray-700">Konfirmasi
                        Password</label>
                    <input type="password" id="register-password-confirm" name="password_confirm" required
                        class="w-full px-5 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-teal-400" />
                </div>
                <input type="hidden" name="role" value="pelapor" />
                <button type="submit" name="register-validate"
                    class="w-full bg-teal-600 text-white py-3 rounded-lg font-semibold hover:bg-teal-700 transition">Register</button>
                <p class="text-center text-gray-600 text-sm mt-4">
                    Sudah punya akun? <a href="#" id="to-login"
                        class="text-teal-600 font-semibold hover:underline">Login di sini</a>
                </p>
            </form>
        </div>
    </div>

    <script>
    // Script Tab Switch (disesuaikan agar bekerja dengan SweetAlert)
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const toRegister = document.getElementById('to-register');
    const toLogin = document.getElementById('to-login');

    // Fungsi untuk mengaktifkan tab Login
    function activateLogin() {
        loginTab.classList.add('tab-active');
        loginTab.classList.remove('tab-inactive');
        registerTab.classList.add('tab-inactive');
        registerTab.classList.remove('tab-active');
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
    }

    // Fungsi untuk mengaktifkan tab Register
    function activateRegister() {
        registerTab.classList.add('tab-active');
        registerTab.classList.remove('tab-inactive');
        loginTab.classList.add('tab-inactive');
        loginTab.classList.remove('tab-active');
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
    }

    // Event Listeners untuk tombol tab
    loginTab.addEventListener('click', activateLogin);
    registerTab.addEventListener('click', activateRegister);
    toRegister.addEventListener('click', (e) => {
        e.preventDefault();
        activateRegister();
    });
    toLogin.addEventListener('click', (e) => {
        e.preventDefault();
        activateLogin();
    });

    // Logika SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        // Data untuk SweetAlert Login
        const loginStatus = '<?php echo $loginStatus; ?>';
        const loginErrorMessage =
            '<?php echo isset($_SESSION["error_message"]) ? htmlspecialchars($_SESSION["error_message"]) : ""; ?>';
        const loginRedirectRole =
            '<?php echo isset($_SESSION["login_redirect"]) ? htmlspecialchars($_SESSION["login_redirect"]) : ""; ?>';

        // Data untuk SweetAlert Register
        const registerStatus = '<?php echo $_SESSION["register_status"] ?? ""; ?>';
        const registerErrorMessage =
            '<?php echo isset($_SESSION["error_message_register"]) ? htmlspecialchars($_SESSION["error_message_register"]) : ""; ?>';


        // --- Handle Login Status ---
        if (loginStatus === 'empty') {
            Swal.fire({
                icon: 'warning',
                title: 'Input Kosong!',
                text: 'NIS/NIP dan kata sandi wajib diisi.',
                confirmButtonText: 'OK'
            });
        } else if (loginStatus === 'not_found') {
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: 'NIS/NIP tidak terdaftar.',
                confirmButtonText: 'OK'
            });
        } else if (loginStatus === 'wrong_password') {
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: 'Kata sandi salah.',
                confirmButtonText: 'OK'
            });
        } else if (loginStatus === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                text: 'Anda akan diarahkan ke dashboard.',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                switch (loginRedirectRole) {
                    case 'kepsek':
                    case 'admin':
                        window.location.href = 'views/admin/dashboard-admin.php';
                        break;
                    case 'siswa':
                    case 'guru':
                        window.location.href = 'index.php';
                        break;
                    default:
                        window.location.href = 'index.php';
                }
            });
        } else if (loginStatus === 'error_general' && loginErrorMessage !== '') {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan Login!',
                text: loginErrorMessage,
                confirmButtonText: 'OK'
            });
        }

        // --- Handle Register Status ---
        if (registerStatus !== '' && registerStatus !== 'success') {
            activateRegister();
            if (registerStatus === 'error') {
                Swal.fire({
                    icon: 'error',
                    title: 'Terjadi Kesalahan Pendaftaran!',
                    text: registerErrorMessage,
                    confirmButtonText: 'OK'
                });
            }
        } else if (registerStatus === 'success') {
            activateLogin();
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil!',
                text: 'Akun Anda berhasil dibuat. Silakan login.',
                confirmButtonText: 'OK'
            });
        }

        // Bersihkan session status setelah SweetAlert ditampilkan
        <?php
            unset($_SESSION['login_redirect']);
            unset($_SESSION['error_message']);
            unset($_SESSION['register_status']);
            unset($_SESSION['error_message_register']);
            ?>
    });
    </script>
</body>

</html>