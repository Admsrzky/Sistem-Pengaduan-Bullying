<script>
    <?php if (isset($_SESSION['register_status'])): ?>
        <?php if ($_SESSION['register_status'] === 'success'): ?>
            Swal.fire('Berhasil!', 'Registrasi berhasil. Silakan login.', 'success');
        <?php elseif ($_SESSION['register_status'] === 'exists'): ?>
            Swal.fire('Gagal!', 'NIS/NIP sudah terdaftar.', 'error');
        <?php elseif ($_SESSION['register_status'] === 'mismatch'): ?>
            Swal.fire('Gagal!', 'Konfirmasi password tidak cocok.', 'warning');
        <?php elseif ($_SESSION['register_status'] === 'empty'): ?>
            Swal.fire('Gagal!', 'Semua field wajib diisi.', 'info');
        <?php elseif ($_SESSION['register_status'] === 'error'): ?>
            Swal.fire('Oops!', 'Terjadi kesalahan saat menyimpan data.', 'error');
        <?php endif; ?>
        <?php unset($_SESSION['register_status']); ?>
    <?php endif; ?>
</script>