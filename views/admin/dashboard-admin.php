<?php
// sipeng/views/admin/dashboard.php

// Include the header, which handles session_start(), authentication,
// and sets up $user_id, $user_nama, $user_foto_profile, and database connection ($conn).
include 'header.php';

// Set default timezone for consistent date handling
date_default_timezone_set('Asia/Jakarta');

// Initialize variables for counts
$total_pengaduan = 0;
$status_terkirim = 0;
$status_diproses = 0;
$status_selesai = 0;
$status_ditolak = 0;
$dashboard_error_message = ''; // For any dashboard-specific errors

// Initialize array for latest reports
$latest_laporan_data = [];

// Define the web path for BUKTI (URL) relative to the current file
// Current file: Sipeng/views/admin/dashboard.php
// Uploads folder: Sipeng/uploads/
// To go from dashboard.php to uploads/: ../../uploads/
$asset_base_path_bukti_web = '../../uploads/';

// Ensure database connection is available
if (!isset($conn) || $conn->connect_error) {
    $dashboard_error_message = "Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error');
} else {
    // --- [PERUBAHAN DIMULAI DI SINI] ---
    // --- Query for Dashboard Counts (Optimized into 1 Query) ---
    $sql_status_counts = "SELECT status, COUNT(*) as count FROM laporan GROUP BY status";
    $result_status_counts = mysqli_query($conn, $sql_status_counts);

    if ($result_status_counts) {
        while ($row = mysqli_fetch_assoc($result_status_counts)) {
            // Gunakan switch untuk mengisi variabel yang sudah ada
            switch ($row['status']) {
                case 'terkirim':
                    $status_terkirim = $row['count'];
                    break;
                case 'diproses':
                    $status_diproses = $row['count'];
                    break;
                case 'selesai':
                    $status_selesai = $row['count'];
                    break;
                case 'ditolak':
                    $status_ditolak = $row['count'];
                    break;
            }
        }
        // Hitung total dari hasil yang sudah ada, tanpa perlu query lagi!
        $total_pengaduan = $status_terkirim + $status_diproses + $status_selesai + $status_ditolak;
        mysqli_free_result($result_status_counts);
    } else {
        $dashboard_error_message .= "Gagal mengambil rekap status: " . mysqli_error($conn) . "<br>";
    }
    // --- [PERUBAHAN SELESAI DI SINI] ---


    // --- Prepare Data for Chart ---
    $chart_labels = json_encode(['Terkirim', 'Diproses', 'Selesai', 'Ditolak']);
    $chart_data = json_encode([$status_terkirim, $status_diproses, $status_selesai, $status_ditolak]);
    $chart_colors = json_encode(['#f59e0b', '#8b5cf6', '#10b981', '#ef4444']); // Yellow, Purple, Green, Red (Tailwind colors)

    // --- Fetch Latest Laporan Data for Riwayat Laporan ---
    $sql_latest_laporan = "
        SELECT
            l.id, l.kronologi, l.lokasi, l.tanggal_kejadian, l.bukti, l.status, l.created_at,
            kl.nama_kategori,
            u.nama AS nama_pelapor,
            u.foto_profile AS foto_pelapor_profile
        FROM laporan l
        LEFT JOIN kategori_laporan kl ON l.kategori_id = kl.id
        LEFT JOIN users u ON l.user_id = u.id
        ORDER BY l.created_at DESC
        LIMIT 5"; // Limit to the 5 most recent reports

    $result_latest_laporan = mysqli_query($conn, $sql_latest_laporan);

    if ($result_latest_laporan) {
        while ($row = mysqli_fetch_assoc($result_latest_laporan)) {
            $latest_laporan_data[] = $row;
        }
        mysqli_free_result($result_latest_laporan);
    } else {
        $dashboard_error_message .= "Gagal mengambil riwayat laporan: " . mysqli_error($conn) . "<br>";
    }

    // Close the connection if header.php doesn't keep it open for other parts of the page
    // If header.php closes the connection, remove this line.
    // If other parts of the page need $conn, move mysqli_close($conn) to footer.php.
    mysqli_close($conn);
}
?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
        Dashboard
    </h2>

    <?php if ($dashboard_error_message): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Error!</strong>
            <span class="block sm:inline"><?= htmlspecialchars($dashboard_error_message) ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mt-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-blue-500 p-3 rounded-full">
                <i data-feather="bar-chart-2" class="text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= htmlspecialchars($total_pengaduan) ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400">Total Pengaduan</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-yellow-500 p-3 rounded-full">
                <i data-feather="clock" class="text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= htmlspecialchars($status_terkirim) ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400">Terkirim</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-purple-500 p-3 rounded-full">
                <i data-feather="refresh-cw" class="text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= htmlspecialchars($status_diproses) ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400">Diproses</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-green-500 p-3 rounded-full">
                <i data-feather="check-circle" class="text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= htmlspecialchars($status_selesai) ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400">Selesai</p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md flex items-center">
            <div class="bg-red-500 p-3 rounded-full">
                <i data-feather="x-circle" class="text-white"></i>
            </div>
            <div class="ml-4">
                <p class="text-2xl font-bold text-gray-800 dark:text-white">
                    <?= htmlspecialchars($status_ditolak) ?>
                </p>
                <p class="text-gray-500 dark:text-gray-400">Ditolak</p>
            </div>
        </div>
    </div>

    ---

    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Statistik Pengaduan</h2>
        <div class="relative h-80">
            <canvas id="reportStatusChart"></canvas>
        </div>
    </div>

    ---

    <div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-md">
        <h2 class="px-6 py-4 text-xl font-bold text-gray-800 dark:text-white">Riwayat Laporan Terbaru</h2>
        <div class="overflow-x-auto">
            <table class="w-full whitespace-nowrap">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr
                        class="text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">
                        <th class="px-6 py-3">Thumbnail</th>
                        <th class="px-6 py-3">Judul Laporan</th>
                        <th class="px-6 py-3">Kategori</th>
                        <th class="px-6 py-3">Pelapor</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Tanggal Laporan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y dark:divide-gray-700">
                    <?php if (!empty($latest_laporan_data)): ?>
                        <?php foreach ($latest_laporan_data as $laporan): ?>
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-6 py-4">
                                    <?php
                                    $bukti_filename = $laporan['bukti'] ?? '';
                                    $extension = pathinfo($bukti_filename, PATHINFO_EXTENSION);
                                    $is_video = false;

                                    // Define common video extensions
                                    $video_extensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];

                                    if (!empty($extension) && in_array(strtolower($extension), $video_extensions)) {
                                        $is_video = true;
                                    }

                                    if ($is_video) {
                                        // Display video icon if it's a video file
                                        echo '<div class="flex items-center justify-center w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-md">';
                                        echo '<i data-feather="video" class="text-gray-600 dark:text-gray-400"></i>';
                                        echo '</div>';
                                    } else {
                                        // Display image or "No Img" placeholder
                                        $bukti_url = !empty($bukti_filename) ? $asset_base_path_bukti_web . htmlspecialchars($bukti_filename) : 'https://via.placeholder.com/40?text=No+Img';
                                        echo '<img src="' . $bukti_url . '" alt="Bukti Laporan" class="w-10 h-10 rounded-md object-cover" />';
                                    }
                                    ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars(substr($laporan['kronologi'], 0, 50)) . (strlen($laporan['kronologi']) > 50 ? '...' : '') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars($laporan['nama_kategori'] ?? 'Tidak Diketahui') ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <?php
                                        $pelapor_foto_url = !empty($laporan['foto_profile']) ? $asset_base_path_bukti_web . htmlspecialchars($laporan['foto_profile']) : 'https://i.pravatar.cc/40?img=' . ($laporan['id'] % 20 + 1);
                                        ?>
                                        <img src="<?= $pelapor_foto_url ?>" alt="Author"
                                            class="w-8 h-8 rounded-full object-cover" />
                                        <span class="ml-2"><?= htmlspecialchars($laporan['nama_pelapor'] ?? 'Anonim') ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php
                                    $status_class = '';
                                    switch ($laporan['status']) {
                                        case 'terkirim':
                                            $status_class = 'text-yellow-700 bg-yellow-100 dark:bg-yellow-800 dark:text-yellow-200';
                                            break;
                                        case 'diproses':
                                            $status_class = 'text-purple-700 bg-purple-100 dark:bg-purple-800 dark:text-purple-200';
                                            break;
                                        case 'selesai':
                                            $status_class = 'text-green-700 bg-green-100 dark:bg-green-800 dark:text-green-200';
                                            break;
                                        case 'ditolak':
                                            $status_class = 'text-red-700 bg-red-100 dark:bg-red-800 dark:text-red-200';
                                            break;
                                        default:
                                            $status_class = 'text-gray-700 bg-gray-100 dark:bg-gray-800 dark:text-gray-200';
                                            break;
                                    }
                                    ?>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $status_class ?>">
                                        <?= htmlspecialchars(ucfirst($laporan['status'])) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <?= htmlspecialchars(date('d.m.Y', strtotime($laporan['created_at']))) ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="relative">
                                        <button class="text-gray-500 dark:text-gray-400 focus:outline-none action-btn"
                                            data-id="<?= $laporan['id'] ?>">
                                            <i data-feather="more-horizontal"></i>
                                        </button>
                                        <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hidden z-10 action-dropdown"
                                            id="action-dropdown-<?= $laporan['id'] ?>">
                                            <a href="detail_laporan.php?id=<?= $laporan['id'] ?>"
                                                class="block px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">Lihat
                                                Detail</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="text-gray-700 dark:text-gray-400">
                            <td colspan="7" class="px-6 py-4 text-center">Tidak ada riwayat laporan terbaru.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }

        // Dropdown functionality for table actions
        document.querySelectorAll('.action-btn').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const dropdown = document.getElementById(`action-dropdown-${id}`);
                // Close other open dropdowns
                document.querySelectorAll('.action-dropdown').forEach(d => {
                    if (d !== dropdown) {
                        d.classList.add('hidden');
                    }
                });
                dropdown.classList.toggle('hidden');
            });
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            // Check if the click was outside of any action button or dropdown
            if (!event.target.closest('.action-btn') && !event.target.closest('.action-dropdown')) {
                document.querySelectorAll('.action-dropdown').forEach(dropdown => {
                    dropdown.classList.add('hidden');
                });
            }
        });

        // --- Chart.js Integration ---
        const ctx = document.getElementById('reportStatusChart').getContext('2d');

        // PHP variables for chart data (already JSON encoded in PHP)
        const chartLabels = <?= $chart_labels ?>;
        const chartData = <?= $chart_data ?>;
        const chartColors = <?= $chart_colors ?>;

        new Chart(ctx, {
            type: 'doughnut', // Or 'pie', 'bar' depending on preference
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Jumlah Pengaduan',
                    data: chartData,
                    backgroundColor: chartColors,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false, // Allows chart to fill parent div height
                plugins: {
                    legend: {
                        position: 'bottom', // Position legend at the bottom
                        labels: {
                            color: 'rgb(107, 114, 128)' // Tailwind gray-500
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php include 'footer.php'; ?>