<?php

include 'header.php';

include '../../controllers/UsersController.php';
?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
        Manajemen Data User
    </h2>

    <?php if ($success_message): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Sukses!</strong>
            <span class="block sm:inline"><?= $success_message ?></span>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= $error_message ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-8">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Tambah User Baru</h3>
        <form action="" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="add_nis_nip" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        NIS/NIP
                    </label>
                    <input type="text" id="add_nis_nip" name="nis_nip"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Masukkan NIS/NIP" required>
                </div>
                <div class="mb-4">
                    <label for="add_nama" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" id="add_nama" name="nama"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="mb-4">
                    <label for="add_password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input type="password" id="add_password" name="password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Password" required>
                </div>
                <div class="mb-4">
                    <label for="add_role" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Role
                    </label>
                    <select id="add_role" name="role"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="kepsek">Kepala Sekolah</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
                <div class="mb-4" id="add_jabatan_id_field" style="display: none;">
                    <label for="add_jabatan_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Jabatan (Untuk Guru)
                    </label>
                    <select id="add_jabatan_id" name="add_jabatan_id"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="">Pilih Jabatan</option>
                        <?php foreach ($jabatan_options as $jabatan): ?>
                            <option value="<?= htmlspecialchars($jabatan['id']) ?>">
                                <?= htmlspecialchars($jabatan['nama_jabatan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="add_siswa_fields" style="display: none;"
                    class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-full">
                    <div class="mb-4">
                        <label for="add_jurusan_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Jurusan (Untuk Siswa)
                        </label>
                        <select id="add_jurusan_id" name="add_jurusan_id"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="">Pilih Jurusan</option>
                            <?php foreach ($jurusan_options as $jurusan): ?>
                                <option value="<?= htmlspecialchars($jurusan['id']) ?>">
                                    <?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="add_kelas_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Kelas (Untuk Siswa)
                        </label>
                        <select id="add_kelas_id" name="add_kelas_id"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($kelas_options as $kelas): ?>
                                <option value="<?= htmlspecialchars($kelas['id']) ?>">
                                    <?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4 col-span-full">
                    <label for="add_foto_profile" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Foto Profile (Opsional)
                    </label>
                    <input type="file" id="add_foto_profile" name="foto_profile" accept="image/*"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ukuran maks: 5MB. Format: JPG, JPEG, PNG,
                        GIF.</p>
                </div>
            </div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Tambah User
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daftar User</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            No.
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            NIS/NIP
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Nama
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Role
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Foto Profile
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Created At
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Updated At
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users_data)): ?>
                        <tr>
                            <td colspan="10"
                                class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200 text-center">
                                Tidak ada data user.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; // Initialize $no BEFORE the loop
                        ?>
                        <?php foreach ($users_data as $user): ?>
                            <tr>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= $no++; ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($user['nis_nip'] ?? '') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($user['nama'] ?? '') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars(ucfirst($user['role'] ?? '')) ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <img src="<?= $asset_base_path_users . htmlspecialchars($user['foto_profile'] ?? 'avatar2.png') ?>"
                                        alt="Foto Profil" class="w-10 h-10 rounded-full object-cover">
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($user['created_at'] ? date('d M Y H:i', strtotime($user['created_at'])) : 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($user['updated_at'] ? date('d M Y H:i', strtotime($user['updated_at'])) : 'N/A') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 edit-btn"
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
                                        <form action="" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="delete_id_user"
                                                value="<?= htmlspecialchars($user['id'] ?? '') ?>">
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
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

    <div id="editUserModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit User</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_user" name="edit_id_user">
                <input type="hidden" id="current_nama_display" name="current_nama_display">
                <input type="hidden" id="current_role_display" name="current_role_display">
                <input type="hidden" id="current_foto_profile_display" name="current_foto_profile_display">
                <input type="hidden" id="current_nisnip_display" name="current_nisnip_display">
                <input type="hidden" id="current_jabatan_id_display" name="current_jabatan_id_display">
                <input type="hidden" id="current_jurusan_id_display" name="current_jurusan_id_display">
                <input type="hidden" id="current_kelas_id_display" name="current_kelas_id_display">

                <div class="mb-4">
                    <label for="edit_nisnip" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        NIS/NIP
                    </label>
                    <input type="text" id="edit_nisnip" name="edit_nisnip"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
                </div>
                <div class="mb-4">
                    <label for="edit_nama" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Nama Lengkap
                    </label>
                    <input type="text" id="edit_nama" name="edit_nama"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
                </div>
                <div class="mb-4">
                    <label for="edit_role" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Role
                    </label>
                    <select id="edit_role" name="edit_role"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
                        <option value="admin">Admin</option>
                        <option value="guru">Guru</option>
                        <option value="kepsek">Kepala Sekolah</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
                <div class="mb-4" id="edit_jabatan_id_field" style="display: none;">
                    <label for="edit_jabatan_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Jabatan
                    </label>
                    <select id="edit_jabatan_id" name="edit_jabatan_id"
                        class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="">Pilih Jabatan</option>
                        <?php foreach ($jabatan_options as $jabatan): ?>
                            <option value="<?= htmlspecialchars($jabatan['id']) ?>">
                                <?= htmlspecialchars($jabatan['nama_jabatan']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div id="edit_siswa_fields" style="display: none;">
                    <div class="mb-4">
                        <label for="edit_jurusan_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Jurusan
                        </label>
                        <select id="edit_jurusan_id" name="edit_jurusan_id"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="">Pilih Jurusan</option>
                            <?php foreach ($jurusan_options as $jurusan): ?>
                                <option value="<?= htmlspecialchars($jurusan['id']) ?>">
                                    <?= htmlspecialchars($jurusan['nama_jurusan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="edit_kelas_id"
                            class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                            Kelas
                        </label>
                        <select id="edit_kelas_id" name="edit_kelas_id"
                            class="shadow border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                            <option value="">Pilih Kelas</option>
                            <?php foreach ($kelas_options as $kelas): ?>
                                <option value="<?= htmlspecialchars($kelas['id']) ?>">
                                    <?= htmlspecialchars($kelas['nama_kelas']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="edit_new_password"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        New Password (Kosongkan jika tidak diubah)
                    </label>
                    <input type="password" id="edit_new_password" name="edit_new_password"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        placeholder="Masukkan password baru">
                </div>
                <div class="mb-4">
                    <label for="edit_foto_profile"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Foto Profile Baru (Opsional)
                    </label>
                    <input type="file" id="edit_foto_profile" name="edit_foto_profile" accept="image/*"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Ukuran maks: 5MB. Format: JPG, JPEG, PNG,
                        GIF.</p>
                    <div class="mt-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Foto saat ini:</span>
                        <img id="current_foto_preview" src="" alt="Current Profile"
                            class="w-16 h-16 rounded-full object-cover inline-block ml-2 border border-gray-300">
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeEditModal"
                        class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline dark:bg-gray-600 dark:hover:bg-gray-700 dark:text-white">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</main>

<script src="../../assets/js/DataUsers.js"></script>

<?php include 'footer.php'; ?>