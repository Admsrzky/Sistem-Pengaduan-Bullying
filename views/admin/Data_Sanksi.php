<?php
// 1. Sertakan Controller di bagian paling atas
include '../../controllers/SanksiController.php';

// 2. Sertakan header setelah controller
include 'header.php';

// 3. Ambil status dan pesan dari URL untuk notifikasi SweetAlert
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<main class="flex-1 p-6 overflow-y-auto bg-gray-800">
    <h2 class="text-3xl font-bold text-gray-200 mb-6">
        Manajemen Data Sanksi
    </h2>

    <!-- Form Tambah Sanksi -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg mb-8">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Tambah Sanksi Baru</h3>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <div class="mb-4">
                        <label for="add_laporan_id" class="block text-gray-400 text-sm font-bold mb-2">Laporan
                            Terkait</label>
                        <select id="add_laporan_id" name="laporan_id" required
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Laporan (Hanya yang berstatus "diproses")</option>
                            <?php foreach ($laporan_options as $laporan): ?>
                                <option value="<?= htmlspecialchars($laporan['id']) ?>">
                                    ID: <?= htmlspecialchars($laporan['id']) ?> -
                                    <?= htmlspecialchars(substr($laporan['kronologi'], 0, 50)) ?>...
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="add_jenis_sanksi" class="block text-gray-400 text-sm font-bold mb-2">Jenis
                            Sanksi</label>
                        <select id="add_jenis_sanksi" name="jenis_sanksi" required
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Jenis Sanksi</option>
                            <option value="Ringan">Ringan</option>
                            <option value="Berat">Berat</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label for="add_deskripsi" class="block text-gray-400 text-sm font-bold mb-2">Deskripsi</label>
                        <textarea id="add_deskripsi" name="deskripsi" rows="4"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Masukkan deskripsi sanksi"></textarea>
                    </div>
                </div>
                <div>
                    <div class="mb-4">
                        <label for="add_tanggal_mulai" class="block text-gray-400 text-sm font-bold mb-2">Tanggal
                            Mulai</label>
                        <input type="date" id="add_tanggal_mulai" name="tanggal_mulai"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>
                    <div class="mb-4">
                        <label for="add_tanggal_selesai" class="block text-gray-400 text-sm font-bold mb-2">Tanggal
                            Selesai (Opsional)</label>
                        <input type="date" id="add_tanggal_selesai" name="tanggal_selesai"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label for="add_diberikan_oleh" class="block text-gray-400 text-sm font-bold mb-2">Diberikan
                            Oleh</label>
                        <input type="text" id="add_diberikan_oleh" name="diberikan_oleh"
                            class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Nama pemberi sanksi" value="Guru Bk" required>
                    </div>
                </div>
            </div>
            <button type="submit"
                class="mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-transform transform hover:scale-105">
                Tambah Sanksi
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Sanksi -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Daftar Sanksi</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Laporan</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Jenis Sanksi</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Masa Berlaku</th>
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
                    <?php elseif (empty($sanksi_data)): ?>
                        <tr>
                            <td colspan="4" class="px-5 py-5 text-center text-gray-500">Tidak ada data sanksi.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($sanksi_data as $sanksi): ?>
                            <tr class="hover:bg-gray-800">
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-200 whitespace-no-wrap font-semibold">ID:
                                        <?= htmlspecialchars($sanksi['laporan_id']) ?></p>
                                    <p class="text-gray-500 whitespace-no-wrap">
                                        <?= htmlspecialchars(substr($sanksi['laporan_kronologi'], 0, 50)) ?>...</p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-300"><?= htmlspecialchars($sanksi['jenis_sanksi']) ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-300"><?= date('d M Y', strtotime($sanksi['tanggal_mulai'])) ?> -
                                        <?= $sanksi['tanggal_selesai'] ? date('d M Y', strtotime($sanksi['tanggal_selesai'])) : 'Selesai' ?>
                                    </p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm text-center">
                                    <div class="flex items-center justify-center space-x-4">
                                        <button class="text-blue-400 hover:text-blue-300 edit-btn"
                                            data-id="<?= htmlspecialchars($sanksi['id']) ?>"
                                            data-laporan_id="<?= htmlspecialchars($sanksi['laporan_id']) ?>"
                                            data-jenis_sanksi="<?= htmlspecialchars($sanksi['jenis_sanksi']) ?>"
                                            data-deskripsi="<?= htmlspecialchars($sanksi['deskripsi']) ?>"
                                            data-tanggal_mulai="<?= htmlspecialchars($sanksi['tanggal_mulai']) ?>"
                                            data-tanggal_selesai="<?= htmlspecialchars($sanksi['tanggal_selesai']) ?>"
                                            data-diberikan_oleh="<?= htmlspecialchars($sanksi['diberikan_oleh']) ?>">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>
                                        <form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_sanksi"
                                                value="<?= htmlspecialchars($sanksi['id']) ?>">
                                            <button type="submit" class="text-red-400 hover:text-red-300"><i
                                                    data-feather="trash-2" class="w-5 h-5"></i></button>
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

    <!-- Modal Edit Sanksi (Diperbarui) -->
    <div id="editSanksiModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
        <div class="bg-gray-900 p-8 rounded-xl shadow-lg w-full max-w-lg border border-gray-700">
            <h3 class="text-2xl font-semibold text-gray-200 mb-5">Edit Sanksi</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_sanksi" name="id_sanksi">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="edit_laporan_id" class="block text-gray-400 text-sm font-bold mb-2">Laporan
                                Terkait</label>
                            <select id="edit_laporan_id" name="laporan_id" required
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Laporan</option>
                                <?php
                                // Gabungkan laporan saat ini (jika sudah selesai) dengan laporan yang masih diproses
                                $all_laporan_options = $laporan_options;
                                ?>
                                <?php foreach ($all_laporan_options as $laporan): ?>
                                    <option value="<?= htmlspecialchars($laporan['id']) ?>">ID:
                                        <?= htmlspecialchars($laporan['id']) ?> -
                                        <?= htmlspecialchars(substr($laporan['kronologi'], 0, 50)) ?>...</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_jenis_sanksi" class="block text-gray-400 text-sm font-bold mb-2">Jenis
                                Sanksi</label>
                            <select id="edit_jenis_sanksi" name="jenis_sanksi" required
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="Ringan">Ringan</option>
                                <option value="Berat">Berat</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_deskripsi"
                                class="block text-gray-400 text-sm font-bold mb-2">Deskripsi</label>
                            <textarea id="edit_deskripsi" name="deskripsi" rows="4"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label for="edit_tanggal_mulai" class="block text-gray-400 text-sm font-bold mb-2">Tanggal
                                Mulai</label>
                            <input type="date" id="edit_tanggal_mulai" name="tanggal_mulai"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_tanggal_selesai" class="block text-gray-400 text-sm font-bold mb-2">Tanggal
                                Selesai</label>
                            <input type="date" id="edit_tanggal_selesai" name="tanggal_selesai"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                title: "<?= $status === 'success' ? 'Berhasil!' : 'Gagal!' ?>",
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
                    text: "Data sanksi ini akan dihapus permanen!",
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

        // Script untuk Modal Edit
        const editModal = document.getElementById('editSanksiModal');
        const closeEditModalBtn = document.getElementById('closeEditModal');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;
                document.getElementById('edit_id_sanksi').value = data.id;
                document.getElementById('edit_laporan_id').value = data.laporan_id;
                document.getElementById('edit_jenis_sanksi').value = data.jenis_sanksi;
                document.getElementById('edit_deskripsi').value = data.deskripsi;
                document.getElementById('edit_tanggal_mulai').value = data.tanggal_mulai;
                document.getElementById('edit_tanggal_selesai').value = data.tanggal_selesai;

                editModal.classList.remove('hidden');
            });
        });

        closeEditModalBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });
    });
</script>

<?php include 'footer.php'; ?>