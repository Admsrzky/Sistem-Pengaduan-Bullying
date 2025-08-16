<?php
// 1. Sertakan Controller di bagian paling atas
include '../../controllers/UsersController.php';

// 2. Sertakan header setelah controller
include 'header.php';

// 3. Ambil status dan pesan dari URL untuk notifikasi SweetAlert
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<main class="flex-1 p-6 overflow-y-auto bg-gray-800">
    <h2 class="text-3xl font-bold text-gray-200 mb-6">
        Manajemen Data User
    </h2>

    <!-- Form Tambah User -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg mb-8">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Tambah User Baru</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div>
                    <div class="mb-4">
                        <label for="add_nis_nip" class="block text-gray-400 text-sm font-bold mb-2">NIS/NIP</label>
                        <input type="text" id="add_nis_nip" name="nis_nip"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan NIS/NIP" required>
                    </div>
                    <div class="mb-4">
                        <label for="add_nama" class="block text-gray-400 text-sm font-bold mb-2">Nama Lengkap</label>
                        <input type="text" id="add_nama" name="nama"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan nama lengkap" required>
                    </div>
                    <div class="mb-4">
                        <label for="add_password" class="block text-gray-400 text-sm font-bold mb-2">Password</label>
                        <input type="password" id="add_password" name="password"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Password" required>
                    </div>
                    <div class="mb-4">
                        <label for="add_foto_profile" class="block text-gray-400 text-sm font-bold mb-2">Foto Profile
                            (Opsional)</label>
                        <input type="file" id="add_foto_profile" name="foto_profile" accept="image/*"
                            class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                        <p class="text-xs text-gray-500 mt-1">Maks: 5MB. Format: JPG, PNG, GIF.</p>
                    </div>
                </div>
                <!-- Kolom Kanan -->
                <div>
                    <div class="mb-4">
                        <label for="add_role" class="block text-gray-400 text-sm font-bold mb-2">Role</label>
                        <select id="add_role" name="role"
                            class="bg-gray-800 border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="">Pilih Role</option>
                            <option value="admin">Admin</option>
                            <option value="guru">Guru</option>
                            <option value="kepsek">Kepala Sekolah</option>
                            <option value="siswa">Siswa</option>
                        </select>
                    </div>
                    <div class="mb-4 hidden" id="add_jabatan_id_field">
                        <label for="add_jabatan_id" class="block text-gray-400 text-sm font-bold mb-2">Jabatan (Untuk
                            Guru)</label>
                        <select id="add_jabatan_id" name="add_jabatan_id"
                            class="bg-gray-800 border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Jabatan</option>
                            <?php foreach ($jabatan_options as $jabatan): ?>
                            <option value="<?= htmlspecialchars($jabatan['id']) ?>">
                                <?= htmlspecialchars($jabatan['nama_jabatan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hidden" id="add_siswa_fields">
                        <div class="mb-4">
                            <label for="add_jurusan_id" class="block text-gray-400 text-sm font-bold mb-2">Jurusan
                                (Untuk Siswa)</label>
                            <select id="add_jurusan_id" name="add_jurusan_id"
                                class="bg-gray-800 border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Jurusan</option>
                                <?php foreach ($jurusan_options as $jurusan): ?>
                                <option value="<?= htmlspecialchars($jurusan['id']) ?>">
                                    <?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="add_kelas_id" class="block text-gray-400 text-sm font-bold mb-2">Kelas (Untuk
                                Siswa)</label>
                            <select id="add_kelas_id" name="add_kelas_id"
                                class="bg-gray-800 border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Kelas</option>
                                <?php foreach ($kelas_options as $kelas): ?>
                                <option value="<?= htmlspecialchars($kelas['id']) ?>">
                                    <?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <button type="submit"
                class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-transform transform hover:scale-105">
                Tambah User
            </button>
        </form>
    </div>

    <!-- Tabel Daftar User -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Daftar User</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            User</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Role</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fetch_error)): ?>
                    <tr>
                        <td colspan="4" class="px-5 py-5 text-center text-red-400"><?= htmlspecialchars($fetch_error) ?>
                        </td>
                    </tr>
                    <?php elseif (empty($users_data)): ?>
                    <tr>
                        <td colspan="4" class="px-5 py-5 text-center text-gray-500">Tidak ada data user.</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users_data as $user): ?>
                    <tr class="hover:bg-gray-800">
                        <td class="px-5 py-4 border-b border-gray-700 text-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 w-10 h-10">
                                    <img class="w-full h-full rounded-full object-cover"
                                        src="<?= $asset_base_path_users . htmlspecialchars($user['foto_profile'] ?? 'default.png') ?>"
                                        alt="Foto Profil">
                                </div>
                                <div class="ml-3">
                                    <p class="text-gray-200 whitespace-no-wrap font-semibold">
                                        <?= htmlspecialchars($user['nama'] ?? '') ?></p>
                                    <p class="text-gray-500 whitespace-no-wrap">
                                        <?= htmlspecialchars($user['nis_nip'] ?? '') ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-700 text-sm">
                            <p class="text-gray-300 whitespace-no-wrap">
                                <?= htmlspecialchars(ucfirst($user['role'] ?? '')) ?></p>
                        </td>
                        <td class="px-5 py-4 border-b border-gray-700 text-sm text-center">
                            <div class="flex items-center justify-center space-x-4">
                                <button
                                    class="text-blue-400 hover:text-blue-300 transition-colors duration-300 edit-btn"
                                    data-id="<?= htmlspecialchars($user['id'] ?? '') ?>"
                                    data-nisnip="<?= htmlspecialchars($user['nis_nip'] ?? '') ?>"
                                    data-nama="<?= htmlspecialchars($user['nama'] ?? '') ?>"
                                    data-role="<?= htmlspecialchars($user['role'] ?? '') ?>"
                                    data-foto="<?= htmlspecialchars($user['foto_profile'] ?? '') ?>"
                                    data-jabatan="<?= htmlspecialchars($user['jabatan_id'] ?? '') ?>"
                                    data-jurusan="<?= htmlspecialchars($user['jurusan_id'] ?? '') ?>"
                                    data-kelas="<?= htmlspecialchars($user['kelas_id'] ?? '') ?>">
                                    <i data-feather="edit" class="w-5 h-5"></i>
                                </button>
                                <form action="" method="POST" class="delete-form">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="delete_id_user"
                                        value="<?= htmlspecialchars($user['id'] ?? '') ?>">
                                    <button type="submit"
                                        class="text-red-400 hover:text-red-300 transition-colors duration-300">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Edit User (Diperbarui) -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
        <div class="bg-gray-900 p-8 rounded-xl shadow-lg w-full max-w-lg border border-gray-700">
            <h3 class="text-2xl font-semibold text-gray-200 mb-5">Edit User</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_user" name="edit_id_user">
                <!-- Hidden fields for current data -->
                <input type="hidden" id="current_nama_display" name="current_nama_display">
                <input type="hidden" id="current_role_display" name="current_role_display">
                <input type="hidden" id="current_foto_profile_display" name="current_foto_profile_display">
                <input type="hidden" id="current_nisnip_display" name="current_nisnip_display">
                <input type="hidden" id="current_jabatan_id_display" name="current_jabatan_id_display">
                <input type="hidden" id="current_jurusan_id_display" name="current_jurusan_id_display">
                <input type="hidden" id="current_kelas_id_display" name="current_kelas_id_display">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="edit_nisnip" class="block text-gray-400 text-sm font-bold mb-2">NIS/NIP</label>
                            <input type="text" id="edit_nisnip" name="edit_nisnip"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_nama" class="block text-gray-400 text-sm font-bold mb-2">Nama
                                Lengkap</label>
                            <input type="text" id="edit_nama" name="edit_nama"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_role" class="block text-gray-400 text-sm font-bold mb-2">Role</label>
                            <select id="edit_role" name="edit_role"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                                <option value="admin">Admin</option>
                                <option value="guru">Guru</option>
                                <option value="kepsek">Kepala Sekolah</option>
                                <option value="siswa">Siswa</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_new_password" class="block text-gray-400 text-sm font-bold mb-2">Password
                                Baru</label>
                            <input type="password" id="edit_new_password" name="edit_new_password"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Kosongkan jika tidak diubah">
                        </div>
                    </div>
                    <div>
                        <div class="mb-4 hidden" id="edit_jabatan_id_field">
                            <label for="edit_jabatan_id"
                                class="block text-gray-400 text-sm font-bold mb-2">Jabatan</label>
                            <select id="edit_jabatan_id" name="edit_jabatan_id"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Jabatan</option>
                                <?php foreach ($jabatan_options as $jabatan): ?>
                                <option value="<?= htmlspecialchars($jabatan['id']) ?>">
                                    <?= htmlspecialchars($jabatan['nama_jabatan']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="hidden" id="edit_siswa_fields">
                            <div class="mb-4">
                                <label for="edit_jurusan_id"
                                    class="block text-gray-400 text-sm font-bold mb-2">Jurusan</label>
                                <select id="edit_jurusan_id" name="edit_jurusan_id"
                                    class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Jurusan</option>
                                    <?php foreach ($jurusan_options as $jurusan): ?>
                                    <option value="<?= htmlspecialchars($jurusan['id']) ?>">
                                        <?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label for="edit_kelas_id"
                                    class="block text-gray-400 text-sm font-bold mb-2">Kelas</label>
                                <select id="edit_kelas_id" name="edit_kelas_id"
                                    class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Kelas</option>
                                    <?php foreach ($kelas_options as $kelas): ?>
                                    <option value="<?= htmlspecialchars($kelas['id']) ?>">
                                        <?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label for="edit_foto_profile" class="block text-gray-400 text-sm font-bold mb-2">Ganti
                                Foto</label>
                            <input type="file" id="edit_foto_profile" name="edit_foto_profile" accept="image/*"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                            <div class="mt-2 flex items-center">
                                <span class="text-sm text-gray-500 mr-2">Foto saat ini:</span>
                                <img id="current_foto_preview" src="" alt="Foto Saat Ini"
                                    class="w-12 h-12 rounded-full object-cover">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" id="closeEditModal"
                        class="bg-gray-600 hover:bg-gray-700 text-gray-300 font-bold py-2 px-6 rounded-lg">Batal</button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Script Notifikasi dari URL
    <?php if (!empty($status)): ?>
    Swal.fire({
        icon: "<?= htmlspecialchars($status) ?>",
        title: "<?= $status === 'success' ? 'Berhasil!' : ($status === 'info' ? 'Info' : 'Gagal!') ?>",
        text: "<?= htmlspecialchars($msg) ?>",
        background: '#1f2937',
        color: '#d1d5db'
    }).then(() => {
        history.replaceState(null, null, window.location.pathname);
    });
    <?php endif; ?>

    // Script Konfirmasi Hapus
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data user ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#4b5563',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                background: '#1f2937',
                color: '#d1d5db'
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    // Script untuk menampilkan field tambahan berdasarkan role
    const addRoleSelect = document.getElementById('add_role');
    const addJabatanField = document.getElementById('add_jabatan_id_field');
    const addSiswaFields = document.getElementById('add_siswa_fields');

    addRoleSelect.addEventListener('change', function() {
        addJabatanField.classList.toggle('hidden', this.value !== 'guru');
        addSiswaFields.classList.toggle('hidden', this.value !== 'siswa');
    });

    const editRoleSelect = document.getElementById('edit_role');
    const editJabatanField = document.getElementById('edit_jabatan_id_field');
    const editSiswaFields = document.getElementById('edit_siswa_fields');

    editRoleSelect.addEventListener('change', function() {
        editJabatanField.classList.toggle('hidden', this.value !== 'guru');
        editSiswaFields.classList.toggle('hidden', this.value !== 'siswa');
    });

    // Script untuk Modal Edit
    const editModal = document.getElementById('editUserModal');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const assetPath = '<?= $asset_base_path_users ?>';

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;

            // Mengisi form modal
            document.getElementById('edit_id_user').value = data.id;
            document.getElementById('edit_nisnip').value = data.nisnip;
            document.getElementById('edit_nama').value = data.nama;
            document.getElementById('edit_role').value = data.role;

            // Mengisi hidden fields untuk data saat ini
            document.getElementById('current_nisnip_display').value = data.nisnip;
            document.getElementById('current_nama_display').value = data.nama;
            document.getElementById('current_role_display').value = data.role;
            document.getElementById('current_foto_profile_display').value = data.foto;
            document.getElementById('current_jabatan_id_display').value = data.jabatan;
            document.getElementById('current_jurusan_id_display').value = data.jurusan;
            document.getElementById('current_kelas_id_display').value = data.kelas;

            // Menampilkan foto saat ini
            document.getElementById('current_foto_preview').src = assetPath + data.foto;

            // Mengatur pilihan dropdown
            document.getElementById('edit_jabatan_id').value = data.jabatan;
            document.getElementById('edit_jurusan_id').value = data.jurusan;
            document.getElementById('edit_kelas_id').value = data.kelas;

            // Trigger change event untuk menampilkan field yang sesuai
            editRoleSelect.dispatchEvent(new Event('change'));

            editModal.classList.remove('hidden');
        });
    });

    closeEditModalBtn.addEventListener('click', () => {
        editModal.classList.add('hidden');
    });
});
</script>

<?php include 'footer.php'; ?>