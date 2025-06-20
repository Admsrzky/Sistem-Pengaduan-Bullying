<?php
// detail_laporan.php
include 'layout/header.php';
include 'config/database.php';

// Ambil ID laporan dari parameter GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

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
        $status = htmlspecialchars($laporan['status']);
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
            $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi']; // Tambahkan ekstensi video lain jika diperlukan

            if (in_array(strtolower($extension), $imageExtensions)) {
                $mediaType = 'image';
            } elseif (in_array(strtolower($extension), $videoExtensions)) {
                $mediaType = 'video';
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
                    echo $status === 'Terkirim' ? 'bg-yellow-200 text-yellow-800' : ($status === 'Diproses' ? 'bg-blue-200 text-blue-800' : ($status === 'Selesai' ? 'bg-green-200 text-green-800' :
                        'bg-red-200 text-red-800'));
                    ?>">
                    <?= $status ?>
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

        </div>

        <a href="index?page=riwayat-laporan" class="mt-8 block w-max mx-auto px-6 py-3 text-white font-semibold rounded-lg shadow-md
          bg-gradient-to-r from-pink-500 via-pink-600 to-pink-700 hover:from-pink-600 hover:via-pink-700 hover:to-pink-800
          transition duration-300 ease-in-out mb-6">
            &larr; Kembali ke daftar laporan
        </a>
    </div>
</div>

<?php include 'layout/footer.php'; ?>