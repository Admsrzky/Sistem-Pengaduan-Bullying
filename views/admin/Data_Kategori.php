<?php
// 1. Sertakan Controller di bagian paling atas
include '../../controllers/KategoriController.php';

// 2. Sertakan header setelah controller
include 'header.php';

// 3. Ambil status dan pesan dari URL untuk notifikasi SweetAlert
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<main class="flex-1 p-6 overflow-y-auto bg-gray-800">
    <h2 class="text-3xl font-bold text-gray-200 mb-6">
        Manajemen Data Kategori Laporan
    </h2>

    <!-- Form Tambah Kategori -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg mb-8">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Tambah Kategori Baru</h3>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label for="add_nama_kategori" class="block text-gray-400 text-sm font-bold mb-2">
                    Nama Kategori
                </label>
                <input type="text" id="add_nama_kategori" name="nama_kategori"
                    class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Masukkan nama kategori baru" required>
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-transform transform hover:scale-105">
                Tambah Kategori
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Kategori -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Daftar Kategori</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            No.</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Nama Kategori</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fetch_error)): ?>
                        <tr>
                            <td colspan="3" class="px-5 py-5 text-center text-red-400"><?= htmlspecialchars($fetch_error) ?>
                            </td>
                        </tr>
                    <?php elseif (empty($kategori_data)): ?>
                        <tr>
                            <td colspan="3" class="px-5 py-5 text-center text-gray-500">Tidak ada data kategori.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($kategori_data as $kategori): ?>
                            <tr class="hover:bg-gray-800">
                                <td class="px-5 py-4 border-b border-gray-700 text-sm text-gray-300"><?= $no++; ?></td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-200 whitespace-no-wrap">
                                        <?= htmlspecialchars($kategori['nama_kategori']) ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <div class="flex items-center space-x-4">
                                        <button
                                            class="text-blue-400 hover:text-blue-300 transition-colors duration-300 edit-btn"
                                            data-id="<?= htmlspecialchars($kategori['id']) ?>"
                                            data-nama="<?= htmlspecialchars($kategori['nama_kategori']) ?>">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>
                                        <form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_kategori"
                                                value="<?= htmlspecialchars($kategori['id']) ?>">
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

    <!-- Modal Edit Kategori -->
    <div id="editKategoriModal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
        <div class="bg-gray-900 p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-700">
            <h3 class="text-2xl font-semibold text-gray-200 mb-5">Edit Kategori</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_kategori" name="id_kategori">
                <div class="mb-4">
                    <label for="edit_nama_kategori" class="block text-gray-400 text-sm font-bold mb-2">Nama
                        Kategori</label>
                    <input type="text" id="edit_nama_kategori" name="nama_kategori"
                        class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
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
                    text: "Data yang dihapus tidak dapat dikembalikan!",
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
        const editModal = document.getElementById('editKategoriModal');
        const closeEditModalBtn = document.getElementById('closeEditModal');
        const editIdInput = document.getElementById('edit_id_kategori');
        const editNamaInput = document.getElementById('edit_nama_kategori');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                editIdInput.value = this.dataset.id;
                editNamaInput.value = this.dataset.nama;
                editModal.classList.remove('hidden');
            });
        });

        closeEditModalBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });
    });
</script>

<?php include 'footer.php'; ?>