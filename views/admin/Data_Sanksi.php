<?php
include 'header.php'; // Pastikan header.php ada dan menyediakan koneksi $conn

// Include the new SanksiController
include '../../controllers/SanksiController.php';

// Inisialisasi pesan sukses/error (variabel ini akan diatur di SanksiController.php)
$success_message = $_SESSION['success_message'] ?? '';
$error_message = $_SESSION['error_message'] ?? '';

// Hapus pesan dari session setelah dibaca
unset($_SESSION['success_message']);
unset($_SESSION['error_message']);

// Inisialisasi SanksiController
$sanksiController = new SanksiController($conn);

// Ambil data sanksi dari controller (sekarang sudah termasuk kronologi)
$sanksi_data = $sanksiController->getAllSanksi();

// Ambil daftar laporan dari controller untuk dropdown
$laporan_list = $sanksiController->getAllLaporan();
?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
        Manajemen Data Sanksi
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
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Tambah Sanksi Baru</h3>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">

            <div class="mb-4">
                <label for="add_laporan_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Laporan Terkait
                </label>
                <select id="add_laporan_id" name="laporan_id" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="">Pilih Laporan</option>
                    <?php foreach ($laporan_list as $laporan): ?>
                        <option value="<?= htmlspecialchars($laporan['id']) ?>">
                            ID: <?= htmlspecialchars($laporan['id']) ?> - Kronologi:
                            <?= htmlspecialchars(substr($laporan['kronologi'], 0, 50)) ?><?= (strlen($laporan['kronologi']) > 50 ? '...' : '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label for="add_jenis_sanksi" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Jenis Sanksi
                </label>
                <select id="add_jenis_sanksi" name="jenis_sanksi" required
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                    <option value="">Pilih Jenis Sanksi</option>
                    <option value="Ringan">Ringan</option>
                    <option value="Berat">Berat</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="add_deskripsi" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Deskripsi
                </label>
                <textarea id="add_deskripsi" name="deskripsi" rows="3"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Masukkan deskripsi sanksi"></textarea>
            </div>

            <div class="mb-4">
                <label for="add_tanggal_mulai" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Tanggal Mulai
                </label>
                <input type="date" id="add_tanggal_mulai" name="tanggal_mulai"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    required>
            </div>

            <div class="mb-4">
                <label for="add_tanggal_selesai" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Tanggal Selesai (Opsional)
                </label>
                <input type="date" id="add_tanggal_selesai" name="tanggal_selesai"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
            </div>

            <div class="mb-4">
                <label for="add_diberikan_oleh" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Diberikan Oleh
                </label>
                <input type="text" id="add_diberikan_oleh" name="diberikan_oleh"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Nama pemberi sanksi (misal: Guru Bk)" required>
            </div>

            <button type="submit"
                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Tambah Sanksi
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Daftar Sanksi</h3>
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
                            ID Laporan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Kronologi Laporan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Jenis Sanksi
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Tgl Mulai
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Tgl Selesai
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Diberikan Oleh
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-700 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($sanksi_data)): ?>
                        <tr>
                            <td colspan="9"
                                class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200 text-center">
                                Tidak ada data sanksi.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($sanksi_data as $sanksi): ?>
                            <tr>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= $no++; ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($sanksi['laporan_id']) ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?php
                                    // Tampilkan kronologi laporan, potong jika terlalu panjang
                                    $kronologi_display = htmlspecialchars($sanksi['laporan_kronologi'] ?? 'N/A');
                                    if (strlen($kronologi_display) > 70) { // Sesuaikan panjang yang diinginkan
                                        echo substr($kronologi_display, 0, 70) . '...';
                                    } else {
                                        echo $kronologi_display;
                                    }
                                    ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($sanksi['jenis_sanksi']) ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?php
                                    $deskripsi_display = htmlspecialchars($sanksi['deskripsi']);
                                    if (strlen($deskripsi_display) > 70) { // Sesuaikan panjang yang diinginkan
                                        echo substr($deskripsi_display, 0, 70) . '...';
                                    } else {
                                        echo $deskripsi_display;
                                    }
                                    ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($sanksi['tanggal_mulai']) ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($sanksi['tanggal_selesai'] ?? '-') ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm text-gray-900 dark:text-gray-200">
                                    <?= htmlspecialchars($sanksi['diberikan_oleh']) ?>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-sm">
                                    <div class="flex items-center space-x-2">
                                        <button
                                            class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200 edit-btn"
                                            data-id="<?= htmlspecialchars($sanksi['id']) ?>"
                                            data-laporan_id="<?= htmlspecialchars($sanksi['laporan_id']) ?>"
                                            data-jenis_sanksi="<?= htmlspecialchars($sanksi['jenis_sanksi']) ?>"
                                            data-deskripsi="<?= htmlspecialchars($sanksi['deskripsi']) ?>"
                                            data-tanggal_mulai="<?= htmlspecialchars($sanksi['tanggal_mulai']) ?>"
                                            data-tanggal_selesai="<?= htmlspecialchars($sanksi['tanggal_selesai']) ?>"
                                            data-diberikan_oleh="<?= htmlspecialchars($sanksi['diberikan_oleh']) ?>">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>
                                        <form action="" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus sanksi ini?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_sanksi"
                                                value="<?= htmlspecialchars($sanksi['id']) ?>">
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

    <div id="editSanksiModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">Edit Sanksi</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_sanksi" name="id_sanksi">

                <div class="mb-4">
                    <label for="edit_laporan_id" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Laporan Terkait
                    </label>
                    <select id="edit_laporan_id" name="laporan_id"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
                        <option value="">Pilih Laporan</option>
                        <?php foreach ($laporan_list as $laporan): ?>
                            <option value="<?= htmlspecialchars($laporan['id']) ?>">
                                ID: <?= htmlspecialchars($laporan['id']) ?> - Kronologi:
                                <?= htmlspecialchars(substr($laporan['kronologi'], 0, 50)) ?><?= (strlen($laporan['kronologi']) > 50 ? '...' : '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="edit_jenis_sanksi"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Jenis Sanksi
                    </label>
                    <select id="edit_jenis_sanksi" name="jenis_sanksi" required
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
                        <option value="Ringan">Ringan</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Berat">Berat</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="edit_deskripsi" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Deskripsi
                    </label>
                    <textarea id="edit_deskripsi" name="deskripsi" rows="3"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required></textarea>
                </div>

                <div class="mb-4">
                    <label for="edit_tanggal_mulai"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Tanggal Mulai
                    </label>
                    <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                        required>
                </div>

                <div class="mb-4">
                    <label for="edit_tanggal_selesai"
                        class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                        Tanggal Selesai (Opsional)
                    </label>
                    <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai"
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600">
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

<script src="../../assets/js/DataSanksi.js"></script>

<?php include 'footer.php'; ?>