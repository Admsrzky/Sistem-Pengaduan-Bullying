<!-- Script Tab & SweetAlert -->
<script>
    const loginTab = document.getElementById('login-tab');
    const registerTab = document.getElementById('register-tab');
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const toRegister = document.getElementById('to-register');
    const toLogin = document.getElementById('to-login');

    function activateLogin() {
        loginTab.classList.add('tab-active');
        loginTab.classList.remove('tab-inactive');
        registerTab.classList.add('tab-inactive');
        registerTab.classList.remove('tab-active');
        loginForm.classList.remove('hidden');
        registerForm.classList.add('hidden');
    }

    function activateRegister() {
        registerTab.classList.add('tab-active');
        registerTab.classList.remove('tab-inactive');
        loginTab.classList.add('tab-inactive');
        loginTab.classList.remove('tab-active');
        registerForm.classList.remove('hidden');
        loginForm.classList.add('hidden');
    }

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

    <?php if (!empty($loginStatus)): ?>
        <?php if ($loginStatus === 'success'): ?>
            Swal.fire({
                icon: 'success',
                title: 'Login Berhasil!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                <?php
                $role = $_SESSION['login_redirect'];
                switch ($role) {
                    case 'admin':
                        echo "window.location.href = 'views/admin/dashboard-admin.php';";
                        break;
                    case 'kepsek':
                        echo "window.location.href = 'views/kepsek/dashboard-kepsek.php';";
                        break;
                    case 'siswa':
                    case 'guru':
                        echo "window.location.href = 'index.php';";
                        break;
                }
                unset($_SESSION['login_redirect']);
                ?>
            });
        <?php elseif ($loginStatus === 'wrong_password'): ?>
            Swal.fire({
                icon: 'error',
                title: 'Password salah!'
            });
        <?php elseif ($loginStatus === 'not_found'): ?>
            Swal.fire({
                icon: 'error',
                title: 'NIS/NIP tidak ditemukan!'
            });
        <?php elseif ($loginStatus === 'empty'): ?>
            Swal.fire({
                icon: 'warning',
                title: 'NIS/NIP dan Password tidak boleh kosong!'
            });
        <?php endif; ?>
    <?php endif; ?>

    // Aktifkan tab Register jika ada session active_tab
    <?php if (isset($_SESSION['active_tab']) && $_SESSION['active_tab'] === 'register'): ?>
        document.addEventListener('DOMContentLoaded', () => {
            activateRegister();
        });
    <?php unset($_SESSION['active_tab']);
    endif; ?>
</script>