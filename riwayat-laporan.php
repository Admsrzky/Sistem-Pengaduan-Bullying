<?php

include 'layout/header.php';
include 'config/database.php';


// --- PEMERIKSAAN LOGIN SANGAT PENTING DI SINI ---
// Jika user belum login (nis_nip tidak ada di sesi), arahkan ke halaman login
if (!isset($_SESSION['nis_nip']) || empty($_SESSION['nis_nip']) || !isset($_SESSION['id'])) {
    header('Location: login.php'); // Arahkan ke halaman login
    exit(); // Hentikan eksekusi skrip agar tidak ada kode lain yang dijalankan
}
// ----------------------------------------------------

// Ambil user_id dari sesi pengguna yang sedang login
$loggedInUserId = $_SESSION['id']; // Pastikan $_SESSION['id'] sudah diisi saat login

// Status yang digunakan
$statuses = ['Terkirim', 'Diproses', 'Selesai', 'Ditolak'];

// Array untuk menampung laporan per status
$laporanByStatus = [];

foreach ($statuses as $status) {
    // Query untuk ambil laporan per status DAN berdasarkan user_id yang login
    // Menggunakan Prepared Statement untuk keamanan
    $sql = "SELECT * FROM laporan WHERE user_id = ? AND status = ? ORDER BY created_at DESC";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        // 'is' menandakan user_id adalah integer dan status adalah string
        $stmt->bind_param("is", $loggedInUserId, $status);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $laporanByStatus[$status] = [];
            while ($row = $result->fetch_assoc()) {
                $laporanByStatus[$status][] = $row;
            }
        } else {
            // Jika tidak ada laporan untuk status ini atau user ini
            $laporanByStatus[$status] = null; // null menandakan kosong
        }
        $stmt->close(); // Tutup statement
    } else {
        // Handle error jika prepare statement gagal
        error_log("Error preparing SQL query for status '$status': " . $conn->error);
        $laporanByStatus[$status] = null;
        $_SESSION['error'] = "Terjadi kesalahan saat mengambil data laporan.";
    }
}

// Penting: Tutup koneksi database setelah semua query selesai
if (isset($conn)) {
    $conn->close();
}
?>

<div class="min-h-screen bg-gray-50 py-8 px-4 sm:px-6 lg:px-8 flex justify-center">
    <div class="w-full max-w-4xl">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 text-center">Riwayat Laporan Anda</h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg font-medium" role="alert">
                <?= htmlspecialchars($_SESSION['error']) ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div id="tabs"
            class="flex space-x-6 mb-8 text-gray-700 text-sm font-medium border-b border-gray-200 overflow-x-auto">
            <?php foreach ($statuses as $index => $status) : ?>
                <button data-status="<?= htmlspecialchars($status) ?>"
                    class="tab-btn pb-2 border-b-4 <?php echo ($index === 0) ? 'border-green-500 text-green-600' : 'border-transparent text-gray-600 hover:border-gray-300 hover:text-gray-700'; ?> whitespace-nowrap transition-colors duration-300"
                    <?php if ($index === 0) echo 'aria-current="page"'; ?>>
                    <?= htmlspecialchars($status) ?>
                </button>
            <?php endforeach; ?>
        </div>

        <div id="laporanContainer">
            <?php foreach ($statuses as $index => $status) : ?>
                <div class="laporan-status-container <?php echo ($index !== 0) ? 'hidden' : ''; ?>"
                    data-status="<?= htmlspecialchars($status) ?>">
                    <?php if ($laporanByStatus[$status] !== null && !empty($laporanByStatus[$status])) : ?>
                        <?php foreach ($laporanByStatus[$status] as $row) :
                            $mediaPath = "uploads/" . htmlspecialchars($row['bukti']); // Path lengkap ke file media
                            $lokasi = htmlspecialchars($row['lokasi']);
                            $tanggal = date("d M Y H:i", strtotime($row['created_at']));
                            $judul = htmlspecialchars($row['kronologi']);

                            // Deteksi tipe media berdasarkan ekstensi file
                            $mediaType = 'unknown'; // Default
                            if (!empty($row['bukti'])) {
                                $extension = pathinfo($row['bukti'], PATHINFO_EXTENSION);
                                $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                                $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi']; // Tambahkan ekstensi video lain jika diperlukan

                                if (in_array(strtolower($extension), $imageExtensions)) {
                                    $mediaType = 'image';
                                } elseif (in_array(strtolower($extension), $videoExtensions)) {
                                    $mediaType = 'video';
                                }
                            }
                        ?>
                            <a href="detail-laporan.php?id=<?= htmlspecialchars($row['id']) ?>" class="block laporan-card bg-white rounded-xl shadow-lg overflow-hidden mb-6 cursor-pointer
                            hover:shadow-xl transition-shadow duration-300">
                                <div class="relative">
                                    <?php if ($mediaType === 'image'): ?>
                                        <img src="<?= $mediaPath ?>" alt="Gambar Laporan" class="w-full h-56 sm:h-64 object-cover" />
                                    <?php elseif ($mediaType === 'video'): ?>
                                        <video controls
                                            class="w-full h-56 sm:h-64 object-cover bg-black focus:outline-none focus:ring-2 focus:ring-pink-500">
                                            <source src="<?= $mediaPath ?>" type="video/<?= strtolower($extension) ?>">
                                            Browser Anda tidak mendukung tag video.
                                        </video>
                                    <?php else: ?>
                                        <div class="w-full h-56 sm:h-64 bg-gray-200 flex items-center justify-center text-gray-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-2-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            <span class="ml-2">Tidak ada media atau format tidak didukung.</span>
                                        </div>
                                    <?php endif; ?>

                                    <div
                                        class="absolute top-4 left-4 <?php
                                                                        echo ($status == 'Terkirim') ? 'bg-yellow-300 text-yellow-900' : '';
                                                                        echo ($status == 'Diproses') ? 'bg-blue-300 text-blue-900' : '';
                                                                        echo ($status == 'Selesai') ? 'bg-green-300 text-green-900' : '';
                                                                        echo ($status == 'Ditolak') ? 'bg-red-300 text-red-900' : '';
                                                                        ?> text-xs font-semibold px-4 py-1 rounded-full select-none shadow-md">
                                        <?= htmlspecialchars($status) ?>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center px-6 py-4 border-t border-gray-100">
                                    <div
                                        class="flex items-center space-x-2 text-gray-600 text-sm truncate max-w-full sm:max-w-[280px]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0 text-green-700"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 12.414M12 12l-4.243-4.243M6 12a6 6 0 1112 0 6 6 0 01-12 0z" />
                                        </svg>
                                        <span class="truncate" title="<?= $lokasi ?>"><?= $lokasi ?></span>
                                    </div>

                                    <div class="text-gray-400 text-xs whitespace-nowrap select-none mt-2 sm:mt-0">
                                        <?= $tanggal ?>
                                    </div>
                                </div>

                                <div class="px-6 pb-6 pt-2 font-semibold text-gray-900 truncate" title="<?= $judul ?>">
                                    <?= $judul ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="flex flex-col items-center justify-center py-20 bg-white rounded-xl shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-24 h-24 mb-6 text-pink-700 animate-bounce"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 4v16m0 0H8a2 2 0 01-2-2V6a2 2 0 012-2h4z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 8l-4-4v4a2 2 0 004 0z" />
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum ada laporan untuk status ini.</h3>
                            <a href="laporan.php" class="px-6 py-2 rounded text-white font-semibold transition" style="background: linear-gradient(90deg, #e91e63 0%, #d81b60 50%, #c2185b 100%);
                                box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4);">
                                Buat Laporan Baru
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>


<script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-btn');
        const containers = document.querySelectorAll('.laporan-status-container');

        // Fungsi untuk mengaktifkan tab pertama saat halaman dimuat
        function activateFirstTab() {
            if (tabs.length > 0) {
                tabs[0].click(); // Simulasikan klik pada tab pertama
            }
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // Reset tab aktif
                tabs.forEach(t => {
                    t.classList.remove('border-green-500', 'text-green-600');
                    t.classList.add('border-transparent', 'text-gray-600');
                });

                // Set tab aktif yg diklik
                tab.classList.add('border-green-500', 'text-green-600');
                tab.classList.remove('border-transparent', 'text-gray-600');

                const selectedStatus = tab.getAttribute('data-status');

                // Show container yang sesuai status dan sembunyikan yang lain
                containers.forEach(container => {
                    if (container.getAttribute('data-status') === selectedStatus) {
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                });
            });
        });

        // Panggil fungsi untuk mengaktifkan tab pertama saat DOMContentLoaded
        activateFirstTab();
    });
</script>

<?php include 'layout/footer.php'; ?>