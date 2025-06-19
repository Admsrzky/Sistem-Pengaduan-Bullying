<?php
// detail_laporan.php
include 'layout/header.php';
include 'config/database.php';

// Ambil ID laporan dari parameter GET
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// Query ambil data laporan dengan join ke users dan kategori
$sql = "SELECT laporan.*, users.nis_nip, users.nama AS nama_pelapor
        FROM laporan 
        JOIN users ON laporan.user_id = users.id 
        WHERE laporan.id = $id 
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $laporan = $result->fetch_assoc();
    $imageUrl = "uploads/" . htmlspecialchars($laporan['bukti']);
    $status = htmlspecialchars($laporan['status']);
    $lokasi = htmlspecialchars($laporan['lokasi']);
    $tanggal = date("d M Y, H:i", strtotime($laporan['tanggal_kejadian']));
    $judul = htmlspecialchars($laporan['kronologi']);
    $nis_nip = htmlspecialchars($laporan['nis_nip']);
    $nama_pelapor = htmlspecialchars($laporan['nama_pelapor']);
} else {
    echo "<div class='text-center text-red-600 font-semibold mt-10'>Laporan tidak ditemukan.</div>";
    include 'layout/footer.php';
    exit;
}
?>

<div class="min-h-screen bg-gray-50 py-12 px-6 sm:px-12 lg:px-20">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
        <img src="<?= $imageUrl ?>" alt="Bukti Laporan" class="w-full h-80 object-cover rounded-t-2xl" />
        <div class="p-8 space-y-6 border border-gray-300 rounded-lg">

            <!-- Judul dan Status -->
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

            <!-- Detail Laporan -->
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

        <!-- Tombol Kembali -->
        <a href="index?page=riwayat-laporan" class="mt-8 block w-max mx-auto px-6 py-3 text-white font-semibold rounded-lg shadow-md
          bg-gradient-to-r from-pink-500 via-pink-600 to-pink-700 hover:from-pink-600 hover:via-pink-700 hover:to-pink-800
          transition duration-300 ease-in-out mb-6">
            &larr; Kembali ke daftar laporan
        </a>
    </div>
</div>

<?php include 'layout/footer.php'; ?>