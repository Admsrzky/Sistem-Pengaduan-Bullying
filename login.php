<?php
session_start(); // Pastikan session_start() ada di sini atau di file include awal
include 'config/database.php';

// Include PHP logic for authentication. These files should NOT contain <script> tags for SweetAlert.
include './auth/authlogin.php';
include './auth/authregister.php';

// Ini adalah status login yang diatur oleh authlogin.php
// Pastikan variabel $loginStatus diinisialisasi di authlogin.php
// Variabel $_SESSION['error_message'] juga diatur di authlogin.php

// Ambil status aktif tab dari sesi, default ke 'login'
$active_tab_from_session = $_SESSION['active_tab'] ?? 'login';
unset($_SESSION['active_tab']); // Hapus dari session setelah dibaca

// Ambil loginRedirectRole dari sesi, jika ada. Ini penting untuk JS redirect.
$loginRedirectRole = $_SESSION['login_redirect'] ?? '';
// Pastikan $_SESSION['login_redirect'] juga di-unset setelah dibaca
unset($_SESSION['login_redirect']);
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

<body class="bg-gradient-to-br from-teal-50 to-white flex flex-col items-center justify-center min-h-screen p-6">
    <div class="w-full max-w-md text-center mb-8">
        <img src="assets/img/logo.png" alt="Logo SIPENG" class="mx-auto h-24 w-auto mb-4" />
        <h1 class="text-3xl font-extrabold text-teal-700 drop-shadow-md">SIPENG MAN 1 CILEGON</h1>
        <p class="text-teal-600 mt-2 font-medium">Silahkan login dan register untuk melanjutkan</p>
    </div>

    <div class="bg-white rounded-xl shadow-xl w-full max-w-md border border-teal-200">
        <div class="flex border-b border-teal-200">
            <button id="login-tab" class="flex-1 py-4 font-semibold border-b-4 focus:outline-none">Login</button>
            <button id="register-tab" class="flex-1 py-4 font-semibold border-b-4 focus:outline-none">Register</button>
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

        // Logika untuk mengaktifkan tab yang benar saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const activeTabFromSession = '<?php echo $active_tab_from_session; ?>';
            if (activeTabFromSession === 'register') {
                activateRegister();
            } else {
                activateLogin();
            }

            // Data untuk SweetAlert Login
            const loginStatus = '<?php echo $loginStatus ?? ""; ?>';
            const loginErrorMessage =
                '<?php echo isset($_SESSION["error_message"]) ? htmlspecialchars($_SESSION["error_message"]) : ""; ?>';
            // Ambil role redirect dari PHP, penting untuk SweetAlert sukses login
            const loginRedirectRole = '<?php echo $loginRedirectRole; ?>';

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
                // SweetAlert untuk sukses login, lalu redirect menggunakan JavaScript
                Swal.fire({
                    icon: 'success',
                    title: 'Login Berhasil!',
                    text: 'Anda akan diarahkan ke dashboard.',
                    timer: 1500,
                    showConfirmButton: false, // Tidak perlu tombol konfirmasi karena akan redirect otomatis
                    didClose: () => { // Fungsi ini dijalankan setelah SweetAlert tertutup (termasuk oleh timer)
                        let redirectUrl = 'index.php'; // Default redirect
                        switch (loginRedirectRole) {
                            case 'kepsek':
                            case 'admin':
                                redirectUrl = 'views/admin/dashboard-admin.php';
                                break;
                            case 'siswa':
                            case 'guru':
                                redirectUrl = 'index.php'; // Atau halaman spesifik untuk siswa/guru
                                break;
                        }
                        window.location.href = redirectUrl; // Lakukan redirect
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
            if (registerStatus !== '') { // Hanya tampilkan SweetAlert jika ada status register
                activateRegister(); // Selalu aktifkan tab register jika ada status register
                let swalIcon = 'error';
                let swalTitle = 'Pendaftaran Gagal!';
                let swalText = '';

                switch (registerStatus) {
                    case 'success':
                        swalIcon = 'success';
                        swalTitle = 'Pendaftaran Berhasil!';
                        swalText = 'Akun Anda berhasil dibuat. Silakan login.';
                        activateLogin(); // Setelah sukses, arahkan ke tab login
                        break;
                    case 'exists':
                        swalText = 'NIS/NIP sudah terdaftar. Gunakan NIS/NIP lain.';
                        break;
                    case 'mismatch':
                        swalText = 'Konfirmasi kata sandi tidak sesuai.';
                        break;
                    case 'empty':
                        swalText = 'Semua kolom wajib diisi.';
                        break;
                    case 'password_too_short':
                        swalText = 'Kata sandi terlalu pendek. Minimal 8 karakter.';
                        break;
                    case 'password_complexity':
                        swalText = 'Kata sandi harus mengandung kombinasi huruf besar dan angka.';
                        break;
                    case 'error':
                    default:
                        swalText = registerErrorMessage !== '' ? registerErrorMessage :
                            'Terjadi kesalahan saat mendaftarkan akun.';
                        break;
                }

                // Tampilkan SweetAlert hanya jika ada pesan yang perlu ditampilkan (bukan hanya pengalihan tab)
                // Ini untuk mencegah SweetAlert pop up di login success jika itu terjadi setelah register success
                if (!(registerStatus === 'success' && loginStatus === 'success')) {
                    Swal.fire({
                        icon: swalIcon,
                        title: swalTitle,
                        text: swalText,
                        confirmButtonText: 'OK'
                    });
                }
            }

            // Bersihkan session status setelah SweetAlert ditampilkan
            <?php
            // Pastikan Anda hanya meng-unset setelah nilainya DIBACA oleh JavaScript
            // Jika Anda sudah meng-unset di bagian atas setelah dibaca, tidak perlu lagi di sini
            // Namun, jika PHP redirect terjadi sebelum JS sempat membaca, ini akan membantu membersihkan.
            // Untuk memastikan tidak ada refresh, PHP authlogin.php TIDAK boleh ada header('Location') untuk sukses.
            // Hanya untuk kegagalan autentikasi sebelum SweetAlert bisa tampil.
            // Di sini, kita akan mengasumsikan authlogin.php dan authregister.php tidak melakukan redirect untuk kasus sukses,
            // sehingga SweetAlert bisa mengambil alih.
            // Maka, unset $_SESSION['login_redirect'] sudah dilakukan di awal file ini.
            // Cukup unset sisa error messages.
            unset($_SESSION['error_message']);
            unset($_SESSION['register_status']);
            unset($_SESSION['error_message_register']);
            ?>
        });
    </script>
</body>

</html>