<?php
ob_start(); // TAMBAHKAN BARIS INI SEBAGAI BARIS PERTAMA
session_start(); // Pastikan session dimulai di awal setiap halaman yang menggunakannya
include '../../config/database.php';
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'kepsek')) {
    // Redirect to login page if no role is set, or if the role is neither admin nor kepsek
    header('Location: ../../login.php');
    exit(); // Crucial to exit after redirect to prevent further script execution
}

// --- Fungsi Helper untuk Menghitung 'Time Ago' (Waktu yang lalu) ---
function time_ago($datetime, $full = false)
{
    if (!is_string($datetime)) {
        return 'Invalid date';
    }

    $now = new DateTime();
    try {
        $ago = new DateTime($datetime);
    } catch (Exception $e) {
        return 'Invalid date format';
    }

    $diff = $now->diff($ago);

    $weeks = floor($diff->d / 7);
    $diff->d -= $weeks * 7;

    $string = array(
        'y' => 'tahun',
        'm' => 'bulan',
        'w' => 'minggu',
        'd' => 'hari',
        'h' => 'jam',
        'i' => 'menit',
        's' => 'detik',
    );
    foreach ($string as $k => &$v) {
        if ($k === 'w') {
            if ($weeks) {
                $v = $weeks . ' ' . $v . ($weeks > 1 ? '' : '');
            } else {
                unset($string[$k]);
            }
        } elseif ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
}


// --- Ambil 3 Laporan Terbaru dari Database ---
$latest_reports = []; // Inisialisasi variabel sebagai array kosong untuk menyimpan banyak laporan

try {
    // Query untuk mengambil TIGA laporan terbaru berdasarkan created_at
    $stmt = $pdo->query("SELECT id, kronologi, created_at FROM laporan ORDER BY created_at DESC LIMIT 2");
    $results = $stmt->fetchAll(); // Menggunakan fetchAll() untuk mendapatkan semua baris

    if ($results) {
        foreach ($results as $row) {
            $latest_reports[] = [
                'id'       => $row['id'], // Tambahkan ID laporan jika ingin membuat link spesifik
                'subject'  => $row['kronologi'],
                'time_ago' => time_ago($row['created_at'])
            ];
        }
    }
} catch (\PDOException $e) {
    error_log("Error saat mengambil laporan terbaru: " . $e->getMessage());
    $latest_reports = []; // Pastikan array kosong jika ada error
}

// Initialize variables for messages
$success_message = '';
$error_message = '';

// Check for messages passed via session (from previous operations)
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Pastikan koneksi database tersedia sebelum melakukan query
if (isset($conn) && $conn->ping()) {
    $latest_reports_count = 0; // Inisialisasi hitungan

    // Query untuk menghitung jumlah laporan dengan status 'pending'
    $sql_count_latest_reports = "SELECT COUNT(id) AS total_pending FROM laporan WHERE status = 'terkirim'";
    $result_count = mysqli_query($conn, $sql_count_latest_reports);

    if ($result_count) {
        $row_count = mysqli_fetch_assoc($result_count);
        $latest_reports_count = $row_count['total_pending'];
        mysqli_free_result($result_count); // Bebaskan hasil query
    } else {
        // Jika ada error query, catat di log error PHP (penting untuk debugging)
        error_log("Failed to fetch latest reports count: " . mysqli_error($conn));
        // Anda juga bisa mengatur pesan error sesi jika ingin memberitahu user/admin
        // $_SESSION['error_message_header'] = "Gagal memuat jumlah laporan terbaru."; 
    }
} else {
    // Ini menangani kasus jika $conn tidak valid dari awal
    $latest_reports_count = 0; // Default ke 0 jika koneksi gagal
}


// Variabel untuk data pengguna, ambil dari session
// Pastikan semua ini menggunakan null coalescing operator untuk menghindari undefined array key warnings
$user_id = $_SESSION['nis_nip'] ?? null; // Mengambil NIS/NIP dari session
$user_nama = $_SESSION['nama'] ?? 'Pengguna Dashboard'; // Mengambil nama dari session, dengan default jika belum diset
$user_role = $_SESSION['role'] ?? ''; // Mengambil role dari session
$user_foto_profile = $_SESSION['foto_profile'] ?? 'default.png'; // Mengambil nama file foto profil dari session, dengan default

// Path untuk aset gambar profil
$asset_base_path = '../../assets/img/profile/'; // Cilegon/Sipeng/assets/img/profile/

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sistem Pengaduan - MAN 1 CILEGON</title>
    <link rel="shortcut icon" href="../../assets/img/logo.png" type="image/x-icon" />

    <script src="https://cdn.tailwindcss.com"></script>

    <script src="https://unpkg.com/feather-icons"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link href="https://cdn.jsdelivr.net/npm/simple-lightbox@2.14.2/dist/simple-lightbox.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&display=swap" rel="stylesheet">

</head>

<script src="../../assets/js/script.js"></script>

<style>
    /* CSS untuk Profile Picture Container */
    .profile-img-container {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #e2e8f0;
        /* bg-gray-200 */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #cbd5e0;
        /* border-gray-300 */
    }

    .profile-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-img-container input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .profile-img-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .profile-img-container:hover .profile-img-overlay {
        opacity: 1;
    }
</style>
</head>

<body class="bg-gray-100 dark:bg-gray-900">
    <div class="flex h-screen">
        <?php include 'sidebar.php'; ?>
        <div class="flex flex-col flex-1">
            <header class="bg-white dark:bg-gray-800 shadow-md">
                <div class="container mx-auto px-6 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button class="text-gray-500 dark:text-gray-400 focus:outline-none lg:hidden"
                            id="sidebar-toggle">
                            <i data-feather="menu"></i>
                        </button>
                        <!-- <div class="relative hidden sm:block">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <i data-feather="search" class="text-gray-500"></i>
                            </span>
                            <input type="text"
                                class="w-full pl-10 pr-4 py-2 text-gray-700 bg-gray-100 dark:bg-gray-700 dark:text-white border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter keywords ..." />
                        </div> -->
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <?php if ($user_role === 'admin'): ?>
                                <button class="text-gray-500 dark:text-gray-400 focus:outline-none" id="notification-btn">
                                    <i data-feather="bell"></i>
                                    <?php if (!empty($latest_reports)): ?>
                                        <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                                    <?php endif; ?>
                                </button>
                            <?php endif; ?>
                            <div class="absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hidden z-10"
                                id="notification-dropdown">
                                <div class="p-4">
                                    <?php
                                    if (!empty($latest_reports)) {
                                        echo '<p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">Laporan Terbaru:</p>'; // Judul untuk daftar laporan
                                        foreach ($latest_reports as $report) {
                                    ?>
                                            <div
                                                class="flex items-start mb-3 last:mb-0 border-b border-gray-100 dark:border-gray-700 pb-2">
                                                <div class="flex-shrink-0 mt-1">
                                                    <i data-feather="file-text" class="text-blue-500 w-4 h-4"></i>
                                                </div>
                                                <div class="ml-3 flex-1">
                                                    <a href="Data_Pengaduan.php?id=<?= htmlspecialchars($report['id']) ?>"
                                                        class="block">
                                                        <p
                                                            class="text-sm font-medium text-gray-900 dark:text-white leading-tight">
                                                            <?= htmlspecialchars($report['subject'] ?? 'Tanpa Subjek') ?>
                                                        </p>
                                                    </a>
                                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                                        <?= htmlspecialchars($report['time_ago'] ?? 'Baru saja') ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php
                                        }
                                    } else {
                                        ?>
                                        <div class="text-center py-4 text-gray-500 dark:text-gray-400">
                                            Tidak ada laporan baru.
                                        </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <a href="Data_Pengaduan.php"
                                    class="block py-2 text-center text-sm text-blue-500 dark:text-blue-400 bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600">
                                    Lihat Semua Laporan
                                </a>
                            </div>
                        </div>
                        <div class="relative">
                            <button class="flex items-center focus:outline-none" id="profile-btn">
                                <img src="<?= $asset_base_path . htmlspecialchars($user_foto_profile) ?>" alt="User"
                                    class="w-10 h-10 rounded-full" />
                            </button>
                            <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden hidden z-10"
                                id="profile-dropdown">
                                <div class="px-4 py-2 text-sm text-gray-700 dark:text-white">
                                    <p class="font-semibold"><?= htmlspecialchars($user_nama) ?></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        <?= htmlspecialchars(ucfirst($user_role)) ?></p>
                                </div>
                                <div class="border-t border-gray-100 dark:border-gray-700">
                                    <a href="profile_admin.php"
                                        class="flex items-center px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i data-feather="user" class="mr-2"></i> Profile
                                    </a>
                                    <a href="index.php?page=account-setting"
                                        class="flex items-center px-4 py-2 text-gray-700 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i data-feather="settings" class="mr-2"></i> Account settings
                                    </a>
                                    <a href="../../logout.php"
                                        class="flex items-center px-4 py-2 text-red-500 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <i data-feather="log-out" class="mr-2"></i> Log out
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>