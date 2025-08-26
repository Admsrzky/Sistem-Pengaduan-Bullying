<?php
// 1. Sertakan header DULU untuk membuat koneksi database '$conn'
include 'header.php';

// 2. SEKARANG sertakan Controller, yang akan menggunakan '$conn'
include '../../controllers/LaporanController.php';

// 3. Ambil status dan pesan dari URL untuk notifikasi SweetAlert
$status = isset($_GET['status']) ? $_GET['status'] : '';
$msg = isset($_GET['msg']) ? $_GET['msg'] : '';

// Path untuk menampilkan bukti di browser
$asset_base_path_bukti_web = '../../uploads/';
?>

<main class="flex-1 p-6 overflow-y-auto bg-gray-800">
    <h2 class="text-3xl font-bold text-gray-200 mb-6">
        Manajemen Data Pengaduan / Laporan
    </h2>

    <!-- Tabel Daftar Laporan -->
    <div class="bg-gray-900 p-8 rounded-xl shadow-lg">
        <h3 class="text-2xl font-semibold text-gray-200 mb-5">Daftar Laporan</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Pelapor</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Detail Laporan</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-700 bg-gray-800 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">
                            Status</th>
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
                    <?php elseif (empty($laporan_data)): ?>
                        <tr>
                            <td colspan="4" class="px-5 py-5 text-center text-gray-500">Tidak ada data laporan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($laporan_data as $laporan): ?>
                            <tr class="hover:bg-gray-800">
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-200 whitespace-no-wrap font-semibold">
                                        <?= htmlspecialchars($laporan['nama_pelapor'] ?? 'N/A') ?></p>
                                    <p class="text-gray-500 whitespace-no-wrap">
                                        <?= htmlspecialchars($laporan['nisnip_pelapor'] ?? 'N/A') ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <p class="text-gray-300 whitespace-no-wrap font-bold">
                                        <?= htmlspecialchars($laporan['nama_kategori'] ?? 'N/A') ?></p>
                                    <p class="text-gray-400 whitespace-no-wrap mt-1">
                                        <?= htmlspecialchars(mb_strimwidth($laporan['kronologi'] ?? '', 0, 70, "...")) ?></p>
                                    <p class="text-gray-500 text-xs mt-1">Lokasi:
                                        <?= htmlspecialchars($laporan['lokasi'] ?? 'N/A') ?></p>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php
                                        switch ($laporan['status']) {
                                            case 'terkirim':
                                                echo 'bg-blue-800 text-blue-100';
                                                break;
                                            case 'diproses':
                                                echo 'bg-yellow-800 text-yellow-100';
                                                break;
                                            case 'Selesai':
                                                echo 'bg-green-800 text-green-100';
                                                break;
                                            case 'Ditolak':
                                                echo 'bg-red-800 text-red-100';
                                                break;
                                            default:
                                                echo 'bg-gray-700 text-gray-200';
                                                break;
                                        }
                                        ?>">
                                        <?= htmlspecialchars(ucfirst($laporan['status'] ?? 'N/A')) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 border-b border-gray-700 text-sm text-center">
                                    <div class="flex items-center justify-center space-x-4">
                                        <button class="text-blue-400 hover:text-blue-300 edit-btn"
                                            data-id="<?= htmlspecialchars($laporan['id'] ?? '') ?>"
                                            data-kategori_id="<?= htmlspecialchars($laporan['kategori_id'] ?? '') ?>"
                                            data-kronologi="<?= htmlspecialchars($laporan['kronologi'] ?? '') ?>"
                                            data-lokasi="<?= htmlspecialchars($laporan['lokasi'] ?? '') ?>"
                                            data-tanggal_kejadian="<?= htmlspecialchars($laporan['tanggal_kejadian'] ? date('Y-m-d', strtotime($laporan['tanggal_kejadian'])) : '') ?>"
                                            data-bukti="<?= htmlspecialchars($laporan['bukti'] ?? '') ?>"
                                            data-status="<?= htmlspecialchars($laporan['status'] ?? '') ?>">
                                            <i data-feather="edit" class="w-5 h-5"></i>
                                        </button>
                                        <form action="" method="POST" class="delete-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="delete_laporan_id"
                                                value="<?= htmlspecialchars($laporan['id'] ?? '') ?>">
                                            <button type="submit" class="text-red-400 hover:text-red-300"><i
                                                    data-feather="trash-2" class="w-5 h-5"></i></button>
                                        </form>
                                        <?php if ($laporan['status'] !== 'Selesai' && $laporan['status'] !== 'ditolak'): ?>
                                            <button class="text-red-400 hover:text-red-300 reject-btn"
                                                data-id="<?= htmlspecialchars($laporan['id'] ?? '') ?>"
                                                data-status="<?= htmlspecialchars($laporan['status'] ?? '') ?>">
                                                <i data-feather="x-circle" class="w-5 h-5"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div id="reject-modal" class="fixed inset-0 bg-gray-900 bg-opacity-75 hidden items-center justify-center p-4">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl w-full max-w-md">
            <h2 class="text-xl font-bold text-red-400 mb-4">Tolak Laporan</h2>
            <form id="reject-form" action="../../controllers/PengaduanController.php" method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" id="reject-id" name="reject_laporan_id">
                <div class="mb-4">
                    <label for="reject-reason" class="block text-gray-400 text-sm font-bold mb-2">Alasan
                        Penolakan:</label>
                    <textarea id="reject-reason" name="alasan" rows="4"
                        class="shadow appearance-none border border-gray-700 bg-gray-900 rounded w-full py-2 px-3 text-gray-200 leading-tight focus:outline-none focus:shadow-outline"
                        placeholder="Masukkan alasan mengapa laporan ini ditolak..." required></textarea>
                </div>
                <div class="flex items-center justify-end space-x-4">
                    <button type="button" id="cancel-reject-btn"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Edit Laporan -->
    <div id="editLaporanModal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
        <div class="bg-gray-900 p-8 rounded-xl shadow-lg w-full max-w-lg border border-gray-700">
            <h3 class="text-2xl font-semibold text-gray-200 mb-5">Edit Laporan</h3>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_laporan_id" name="edit_laporan_id">
                <input type="hidden" id="current_bukti_file" name="current_bukti_file">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="mb-4">
                            <label for="edit_kategori_id" class="block text-gray-400 text-sm font-bold mb-2">Kategori
                                Laporan</label>
                            <select id="edit_kategori_id" name="edit_kategori_id"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200"
                                required>
                                <?php foreach ($kategori_options as $kategori): ?>
                                    <option value="<?= htmlspecialchars($kategori['id']) ?>">
                                        <?= htmlspecialchars($kategori['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label for="edit_tanggal_kejadian"
                                class="block text-gray-400 text-sm font-bold mb-2">Tanggal Kejadian</label>
                            <input type="date" id="edit_tanggal_kejadian" name="edit_tanggal_kejadian"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_lokasi" class="block text-gray-400 text-sm font-bold mb-2">Lokasi
                                Kejadian</label>
                            <input type="text" id="edit_lokasi" name="edit_lokasi"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200"
                                required>
                        </div>
                        <div class="mb-4">
                            <label for="edit_status" class="block text-gray-400 text-sm font-bold mb-2">Status
                                Laporan</label>
                            <select id="edit_status" name="edit_status"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200"
                                required>
                                <?php foreach ($status_options as $status_opt): ?>
                                    <option value="<?= htmlspecialchars($status_opt) ?>">
                                        <?= htmlspecialchars(ucfirst($status_opt)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="mb-4">
                            <label for="edit_kronologi"
                                class="block text-gray-400 text-sm font-bold mb-2">Kronologi</label>
                            <textarea id="edit_kronologi" name="edit_kronologi" rows="5"
                                class="bg-gray-800 appearance-none border border-gray-600 rounded-lg w-full py-3 px-4 text-gray-200"
                                required></textarea>
                        </div>
                        <div class="mb-4">
                            <label for="edit_bukti_file" class="block text-gray-400 text-sm font-bold mb-2">Ganti File
                                Bukti (Opsional)</label>
                            <input type="file" id="edit_bukti_file" name="edit_bukti_file"
                                accept="image/*,video/*,application/pdf"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:font-semibold file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600">
                            <div class="mt-2" id="current_bukti_preview_container">
                                <span class="text-sm text-gray-500">Bukti saat ini: </span>
                                <a id="current_bukti_display" href="#" target="_blank"
                                    class="ml-2 text-blue-400 hover:underline"></a>
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
                    text: "Laporan ini akan dihapus permanen!",
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
        const editModal = document.getElementById('editLaporanModal');
        const closeEditModalBtn = document.getElementById('closeEditModal');
        const buktiWebPath = '<?= $asset_base_path_bukti_web ?>';

        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const data = this.dataset;

                document.getElementById('edit_laporan_id').value = data.id;
                document.getElementById('edit_kategori_id').value = data.kategori_id;
                document.getElementById('edit_kronologi').value = data.kronologi;
                document.getElementById('edit_lokasi').value = data.lokasi;
                document.getElementById('edit_tanggal_kejadian').value = data.tanggal_kejadian;
                document.getElementById('edit_status').value = data.status;
                document.getElementById('current_bukti_file').value = data.bukti;

                const buktiDisplay = document.getElementById('current_bukti_display');
                const buktiContainer = document.getElementById('current_bukti_preview_container');
                if (data.bukti) {
                    buktiDisplay.href = buktiWebPath + data.bukti;
                    buktiDisplay.textContent = data.bukti;
                    buktiContainer.style.display = 'block';
                } else {
                    buktiContainer.style.display = 'none';
                }

                editModal.classList.remove('hidden');
            });
        });

        closeEditModalBtn.addEventListener('click', () => {
            editModal.classList.add('hidden');
        });
    });
</script>

<!-- Modal Tolak -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const rejectModal = document.getElementById('reject-modal');
        const rejectBtns = document.querySelectorAll('.reject-btn');
        const rejectIdInput = document.getElementById('reject-id');
        const cancelRejectBtn = document.getElementById('cancel-reject-btn');

        rejectBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const laporanId = this.getAttribute('data-id');
                rejectIdInput.value = laporanId;
                rejectModal.classList.remove('hidden');
                rejectModal.classList.add('flex');
            });
        });

        cancelRejectBtn.addEventListener('click', function() {
            rejectModal.classList.add('hidden');
            rejectModal.classList.remove('flex');
        });

        // Opsional: Tutup modal saat mengklik di luar area modal
        window.addEventListener('click', function(event) {
            if (event.target === rejectModal) {
                rejectModal.classList.add('hidden');
                rejectModal.classList.remove('flex');
            }
        });
    });
</script>

<?php include 'footer.php'; ?>