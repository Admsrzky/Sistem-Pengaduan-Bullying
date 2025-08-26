<?php
// detail_laporan.php
include 'layout/header.php';
include 'config/database.php'; // Pastikan ini menyediakan $conn

// Ambil ID laporan dari parameter GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$laporan = null;
$sanksi_data = []; // Inisialisasi array untuk menyimpan data sanksi

// Query ambil data laporan dengan join ke users
$sql = "SELECT laporan.*, users.nis_nip, users.nama AS nama_pelapor
        FROM laporan
        JOIN users ON laporan.user_id = users.id
        WHERE laporan.id = ?
        LIMIT 1";

$stmt = $conn->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $laporan = $result->fetch_assoc();
        $mediaPath = "uploads/" . htmlspecialchars($laporan['bukti']); // Path lengkap ke file media
        $status = htmlspecialchars($laporan['status']); // Nilai status dari DB
        $lokasi = htmlspecialchars($laporan['lokasi']);
        $tanggal = date("d M Y", strtotime($laporan['tanggal_kejadian']));
        $judul = htmlspecialchars($laporan['kronologi']);
        $nis_nip = htmlspecialchars($laporan['nis_nip']);
        $nama_pelapor = htmlspecialchars($laporan['nama_pelapor']);

        // Deteksi tipe media berdasarkan ekstensi file
        $mediaType = 'unknown'; // Default
        if (!empty($laporan['bukti'])) {
            $extension = pathinfo($laporan['bukti'], PATHINFO_EXTENSION);
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi'];
            $pdfExtensions = ['pdf'];

            if (in_array(strtolower($extension), $imageExtensions)) {
                $mediaType = 'image';
            } elseif (in_array(strtolower($extension), $videoExtensions)) {
                $mediaType = 'video';
            } elseif (in_array(strtolower($extension), $pdfExtensions)) {
                $mediaType = 'pdf';
            }
        }

        // --- Perbaikan di sini: Konversi status ke lowercase untuk perbandingan ---
        // Ini memastikan $normalizedStatus selalu lowercase, sesuai dengan ENUM di DB
        $normalizedStatus = strtolower($status);

        // Ambil data sanksi jika status laporan 'diproses' atau 'selesai'
        if ($normalizedStatus === 'diproses' || $normalizedStatus === 'selesai') {
            $sql_sanksi = "SELECT id, jenis_sanksi, deskripsi, tanggal_mulai, tanggal_selesai, diberikan_oleh
                           FROM sanksi
                           WHERE laporan_id = ? ORDER BY tanggal_mulai DESC";
            $stmt_sanksi = $conn->prepare($sql_sanksi);
            if ($stmt_sanksi) {
                $stmt_sanksi->bind_param("i", $id);
                $stmt_sanksi->execute();
                $result_sanksi = $stmt_sanksi->get_result();
                while ($row_sanksi = $result_sanksi->fetch_assoc()) {
                    $sanksi_data[] = $row_sanksi;
                }
                $stmt_sanksi->close();
            } else {
                error_log("Error preparing SQL query for sanksi: " . $conn->error);
            }
        }
    } else {
        echo "<div class='text-center text-red-600 font-semibold mt-10'>Laporan tidak ditemukan.</div>";
        include 'layout/footer.php';
        exit;
    }
    $stmt->close();
} else {
    error_log("Error preparing SQL query for detail laporan: " . $conn->error);
    echo "<div class='text-center text-red-600 font-semibold mt-10'>Terjadi kesalahan saat mengambil data laporan.</div>";
    include 'layout/footer.php';
    exit;
}

if (isset($conn)) {
    $conn->close();
}
?>

<div class="min-h-screen bg-gray-50 py-12 px-6 sm:px-12 lg:px-20">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <?php if ($mediaType === 'image'): ?>
        <img src="<?= $mediaPath ?>" alt="Bukti Laporan" class="w-full h-80 object-cover rounded-t-2xl" />
        <?php elseif ($mediaType === 'video'): ?>
        <video controls
            class="w-full h-80 object-cover rounded-t-2xl bg-black focus:outline-none focus:ring-2 focus:ring-pink-500">
            <source src="<?= $mediaPath ?>" type="video/<?= strtolower($extension) ?>">
            Browser Anda tidak mendukung tag video.
        </video>
        <?php elseif ($mediaType === 'pdf'): ?>
        <div class="w-full h-80 bg-gray-100 flex flex-col items-center justify-center text-gray-500 rounded-t-2xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400 mb-4" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.414L14.586 5A2 2 0 0115 6.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 5a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-lg font-medium">Dokumen PDF tersedia.</span>
            <a href="<?= $mediaPath ?>" target="_blank"
                class="mt-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Lihat PDF</a>
        </div>
        <?php else: ?>
        <div class="w-full h-80 bg-gray-200 flex flex-col items-center justify-center text-gray-500 rounded-t-2xl">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-2-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span class="text-lg font-medium">Tidak ada media atau format tidak didukung.</span>
        </div>
        <?php endif; ?>

        <div class="p-8 space-y-6 border border-gray-300 rounded-lg">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Detail Laporan
                </h1>
                <span class="inline-block px-4 py-2 rounded-full text-sm font-semibold
                    <?php
                    // Pastikan $normalizedStatus digunakan di sini juga untuk konsistensi warna
                    echo $normalizedStatus === 'terkirim' ? 'bg-yellow-200 text-yellow-800' : ($normalizedStatus === 'diproses' ? 'bg-blue-200 text-blue-800' : ($normalizedStatus === 'selesai' ? 'bg-green-200 text-green-800' :
                        'bg-red-200 text-red-800')); // 'ditolak'
                    ?>">
                    <?= htmlspecialchars(ucfirst($status)) ?>
                </span>
            </div>

            <div class="text-gray-700 space-y-2">
                <p><strong>Pelapor:</strong> <span class="font-medium text-gray-900"><?= $nama_pelapor ?></span>
                    (<?= $nis_nip ?>)</p>
                <p><strong>Lokasi Kejadian:</strong> <span class="font-medium"><?= $lokasi ?></span></p>
                <p><strong>Tanggal Kejadian:</strong> <span class="font-medium"><?= $tanggal ?></span></p>
            </div>

            <div>
                <h2 class="text-xl font-semibold text-gray-800 mb-2">Kronologi Kejadian:</h2>
                <p class="text-gray-700 whitespace-pre-wrap leading-relaxed"><?= $judul ?></p>
            </div>

            <?php
            if (isset($laporan) && $laporan['status'] === 'Ditolak'): ?>
            <div class="mt-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                <h3 class="font-bold text-lg">Alasan Penolakan:</h3>
                <p><?= htmlspecialchars($laporan['alasan']) ?></p>
            </div>
            <?php endif; ?>

            <?php
            // --- Tampilkan informasi sanksi jika status laporan 'diproses' atau 'selesai' ---
            if ($normalizedStatus === 'diproses' || $normalizedStatus === 'selesai'): // <-- Sekarang menggunakan $normalizedStatus
            ?>
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Sanksi Terkait:</h2>
                <?php if (!empty($sanksi_data)): ?>
                <div class="space-y-4">
                    <?php foreach ($sanksi_data as $sanksi): ?>
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <p class="text-base font-semibold text-teal-700 mb-1">
                            Jenis Sanksi: <?= htmlspecialchars($sanksi['jenis_sanksi']) ?>
                        </p>
                        <p class="text-sm text-gray-800 mb-2">
                            Deskripsi: <?= htmlspecialchars($sanksi['deskripsi']) ?>
                        </p>
                        <div class="text-xs text-gray-600 flex justify-between">
                            <span>
                                Tanggal Mulai:
                                <?= htmlspecialchars(date("d M Y", strtotime($sanksi['tanggal_mulai']))) ?>
                                <?= !empty($sanksi['tanggal_selesai']) ? ' - Tanggal Selesai: ' . htmlspecialchars(date("d M Y", strtotime($sanksi['tanggal_selesai']))) : '' ?>
                            </span>
                            <span class="font-medium">Diberikan Oleh:
                                <?= htmlspecialchars($sanksi['diberikan_oleh']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-600 italic">Belum ada sanksi yang tercatat untuk laporan ini.</p>
                <?php endif; ?>
            </div>
            <?php endif; // End of if status is diproses or selesai
            ?>

        </div>

        <a href="riwayat-laporan.php" class="mt-8 block w-max mx-auto px-6 py-3 text-white font-semibold rounded-lg shadow-md
            bg-gradient-to-r from-pink-500 via-pink-600 to-pink-700 hover:from-pink-600 hover:via-pink-700 hover:to-pink-800
            transition duration-300 ease-in-out mb-6">
            &larr; Kembali ke daftar laporan
        </a>
    </div>
</div>

<?php include 'layout/footer.php'; ?>