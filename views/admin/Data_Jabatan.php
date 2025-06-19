<?php

include 'header.php';

include '../../controllers/JabatanController.php'; // Ensure this path is correct
?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
        Manajemen Data Jabatan Guru
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
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Tambah Jabatan Baru</h3>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label for="add_nama_jabatan" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Nama Jabatan
                </label>
                <input type="text" id="add_nama_jabatan" name="nama_jabatan"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Masukkan nama jabatan baru" required>
            </div>
            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Tambah Jabatan
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daftar Jabatan</h3>
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
                            Nama Jabatan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($jabatan_data)): ?>
                    <tr>
                        <td colspan="3"
                            class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200 text-center">
                            Tidak ada data jabatan.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php $no = 1; // Inisialisasi $no di luar loop 
                        ?>
                    <?php foreach ($jabatan_data as $jabatan): ?>
                    <tr>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                            <?= $no++; ?>
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                            <?= htmlspecialchars($jabatan['nama_jabatan']) ?>
                        </td>
                        <td
                            class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                            <div class="flex items-center space-x-2">
                                <button
                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 edit-btn"
                                    data-id="<?= htmlspecialchars($jabatan['id']) ?>"
                                    data-nama="<?= htmlspecialchars($jabatan['nama_jabatan']) ?>">
                                    <i data-feather="edit" class="w-5 h-5"></i>
                                </button>
                                <form action="" method="POST"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus jabatan ini? Guru yang terkait dengan jabatan ini mungkin terpengaruh.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id_jabatan"
                                        value="<?= htmlspecialchars($jabatan['id']) ?>">
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

    <div id="editJabatanModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit Jabatan</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_jabatan" name="id_jabatan">
                <div class="mb-4">
                    <label for="edit_nama_jabatan"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Nama Jabatan
                    </label>
                    <input type="text" id="edit_nama_jabatan" name="nama_jabatan"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
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

<script src="../../assets/js/DataJabatan.js"></script>

<?php include 'footer.php'; ?>