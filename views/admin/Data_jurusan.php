<?php
// 1. Sertakan Controller di bagian paling atas
include '../../controllers/JurusanController.php';

// 2. Sertakan header setelah controller
include 'header.php';

// 3. Ambil status dan pesan dari URL untuk notifikasi SweetAlert
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!-- Mengembalikan background gelap seperti pada screenshot Anda -->
<main class="flex-1 p-6 overflow-y-auto bg-gray-800">
    <h2 class="text-3xl font-bold text-gray-200 mb-6">
        Manajemen Data Jurusan
    </h2>

    <!-- Form Tambah Jurusan dengan teks yang lebih cerah -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg mb-8">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Tambah Jurusan Baru</h3>
        <form action="" method="POST">
            <input type="hidden" name="action" value="add">
            <div class="mb-4">
                <label for="add_nama_jurusan" class="block text-gray-400 text-sm font-bold mb-2">
                    Nama Jurusan
                </label>
                <input type="text" id="add_nama_jurusan" name="nama_jurusan"
                    class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Contoh: Rekayasa Perangkat Lunak" required>
            </div>
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg focus:outline-none focus:shadow-outline transition-transform transform hover:scale-105">
                Tambah Jurusan
            </button>
        </form>
    </div>

    <!-- Tabel Daftar Jurusan dengan teks yang lebih cerah -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Daftar Jurusan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            No.
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Nama Jurusan
                        </th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($fetch_error)): ?>
                        <tr>
                            <td colspan="3" class="px-5 py-5 text-center text-red-400">
                                <?= htmlspecialchars($fetch_error) ?>
                            </td>
                        </tr>
                    <?php elseif (empty($jurusan_data)): ?>
                        <tr>
                            <td colspan="3" class="px-5 py-5 text-center text-gray-500">
                                Tidak ada data jurusan.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; ?>
                        <?php foreach ($jurusan_data as $jurusan): ?>
                            <tr class="hover:bg-gray-800">
                                <td class="px-5 py-4 border-b border-gray-700 text-sm text-gray-300"><?= $no++; ?></td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-200 whitespace-no-wrap">
                                        <?= htmlspecialchars($jurusan['nama_jurusan']) ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <div class="flex items-center space-x-4">
                                        <!-- Tombol Edit -->
                                        <button
                                            class="text-blue-400 hover:text-blue-300 transition-colors duration-300 edit-btn"
                                            data-id="<?= htmlspecialchars($jurusan['id']) ?>"
                                            data-nama="<?= htmlspecialchars($jurusan['nama_jurusan']) ?>">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>

                                        <!-- Form Hapus -->
                                        <form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id_jurusan"
                                                value="<?= htmlspecialchars($jurusan['id']) ?>">
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

    <!-- Modal Edit Jurusan -->
    <div id="editJurusanModal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
        <div class="bg-gray-900 p-8 rounded-xl shadow-lg w-full max-w-md border border-gray-700">
            <h3 class="text-2xl font-semibold text-gray-200 mb-5">Edit Jurusan</h3>
            <form action="" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_jurusan" name="id_jurusan">
                <div class="mb-4">
                    <label for="edit_nama_jurusan" class="block text-gray-400 text-sm font-bold mb-2">Nama
                        Jurusan</label>
                    <input type="text" id="edit_nama_jurusan" name="nama_jurusan"
                        class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200 leading-tight focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div class="flex justify-end space-x-3">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Script notifikasi dan modal (tidak ada perubahan, sudah bagus)
        <?php if (!empty($status)): ?>
            Swal.fire({
                icon: "<?= htmlspecialchars($status) ?>",
                title: "<?= $status === 'success' ? 'Berhasil!' : 'Gagal!' ?>",
                text: "<?= htmlspecialchars($msg) ?>",
                showConfirmButton: true,
                background: '#1f2937', // Dark background for SweetAlert
                color: '#d1d5db' // Light text for SweetAlert
            }).then(() => {
                history.replaceState(null, null, window.location.pathname);
            });
        <?php endif; ?>

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
                    background: '#1f2937', // Dark background
                    color: '#d1d5db' // Light text
                }).then(result => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });

        const editModal = document.getElementById('editJurusanModal');
        const closeEditModalBtn = document.getElementById('closeEditModal');
        const editIdInput = document.getElementById('edit_id_jurusan');
        const editNamaInput = document.getElementById('edit_nama_jurusan');

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', () => {
                editIdInput.value = button.dataset.id;
                editNamaInput.value = button.dataset.nama;
                editModal.classList.remove('hidden');
            });
        });

        closeEditModalBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });
    });
</script>

<?php include 'footer.php'; ?>