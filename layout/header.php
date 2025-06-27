<?php
ob_start(); // TAMBAHKAN BARIS INI SEBAGAI BARIS PERTAMA
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sistem Pengaduan - MAN 1 Cilegon</title>
    <link rel="shortcut icon" href="./assets/img/logo.png" type="image/x-icon" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet" />

    <script src="https://unpkg.com/feather-icons"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        /* Scrollbar kecil dan halus untuk tab */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f9fafb;
        }

        /* Ganti warna track scrollbar di dark mode */
        html.dark ::-webkit-scrollbar-track {
            background: #1f2937;
            /* gray-800 */
        }

        ::-webkit-scrollbar-thumb {
            background: #db2777;
            /* pink-600 */
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #be185d;
            /* pink-700 */
        }

        /* Ganti warna thumb scrollbar di dark mode */
        html.dark ::-webkit-scrollbar-thumb {
            background: #a855f7;
            /* purple-500 */
        }

        /* Kelas untuk rotasi ikon chevron-down di FAQ */
        .rotate-180 {
            transform: rotate(180deg);
        }

        .group-open\:rotate-180 {
            transition: transform 0.3s ease-in-out;
        }

        /* Style untuk dropdown (jika tidak menggunakan Alpine.js sepenuhnya untuk semua dropdown) */
        .dropdown-menu {
            display: none;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 font-sans transition-colors duration-300 dark:bg-gray-900 dark:text-white">
    <header class="bg-gradient-to-r from-purple-700 via-pink-600 to-pink-500 text-white shadow-md sticky top-0 z-50
                   dark:bg-gray-800 dark:from-gray-800 dark:via-gray-800 dark:to-gray-800">
        <div class="container mx-auto flex justify-between items-center px-6 py-4">
            <div class="flex items-center space-x-3">
                <img src="./assets/img/logo.png" alt="SIPENG Logo" class="h-9 w-auto object-contain" />
                <a href="index.php" class="focus:outline-none transition-transform duration-200 hover:scale-105">
                    <h1 class="text-2xl font-extrabold tracking-tight leading-none">
                        SIPENG <span class="font-bold">MAN 1 CILEGON</span>
                    </h1>
                </a>
            </div>

            <div class="flex items-center space-x-6">
                <nav class="space-x-6 hidden md:flex font-semibold">
                    <a href="kategori.php"
                        class="hover:underline hover:text-pink-100 transition-colors duration-200">Kategori</a>
                    <a href="keunggulan.php"
                        class="hover:underline hover:text-pink-100 transition-colors duration-200">Keunggulan</a>
                    <a href="cara_kerja.php"
                        class="hover:underline hover:text-pink-100 transition-colors duration-200">Cara Kerja</a>
                    <a href="sanksi.php"
                        class="hover:underline hover:text-pink-100 transition-colors duration-200">Sanksi</a>
                    <a href="testimoni.php"
                        class="hover:underline hover:text-pink-100 transition-colors duration-200">Testimoni</a>
                    <a href="FAQ.php" class="hover:underline hover:text-pink-100 transition-colors duration-200">FAQ</a>
                    <a href="kontak.php"
                        class="hover:underline hover:text-pink-100 transition-colors duration-200">Kontak</a>

                    <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'siswa' || $_SESSION['role'] === 'guru')): ?>
                        <a href="index.php?page=riwayat-laporan"
                            class="hover:underline hover:text-pink-100 transition-colors duration-200">Lihat Laporan</a>
                    <?php endif; ?>
                </nav>

                <!-- <button class="text-white focus:outline-none theme-switcher" id="theme-switcher">
                    <i data-feather="sun" class="block dark:hidden"></i>
                    <i data-feather="moon" class="hidden dark:block"></i>
                </button> -->


                <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'siswa' || $_SESSION['role'] === 'guru')): ?>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <img src="assets/img/profile/<?= htmlspecialchars($_SESSION['foto_profile'] ?? 'default.png') ?>"
                                alt="Avatar" class="w-9 h-9 rounded-full object-cover border-2 border-white shadow-md" />
                            <span class="hidden md:flex flex-col font-medium leading-tight text-white">
                                <?= htmlspecialchars($_SESSION['nama'] ?? 'Pelapor') ?>
                                <span class="text-xs text-left text-pink-100">
                                    <?= htmlspecialchars($_SESSION['role'] ?? '') ?>
                                </span>
                            </span>
                        </button>

                        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-3 w-48 bg-white text-gray-800 rounded-md shadow-lg py-2 z-50
                               dark:bg-gray-700 dark:text-white">
                            <a href="index.php?page=profile"
                                class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <i class="fas fa-user mr-2 text-gray-600 dark:text-gray-300"></i> Profile
                            </a>
                            <a href="index.php?page=account-setting"
                                class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600">
                                <i class="fas fa-cog mr-2 text-gray-600 dark:text-gray-300"></i> Account settings
                            </a>
                            <a href="index.php?page=logout" onclick="return confirm('Apakah Anda yakin ingin logout?')"
                                class="flex items-center px-4 py-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900 dark:text-red-400">
                                <i class="fas fa-sign-out-alt mr-2"></i> Log out
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <button id="menu-btn" class="focus:outline-none md:hidden" x-data="{ open: false }"
                        @click="open = !open" :class="{'text-pink-100': open}">
                        <i class="fas fa-bars text-xl" x-show="!open"></i>
                        <i class="fas fa-times text-xl" x-show="open" x-transition></i>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <div id="mobile-menu" class="hidden bg-pink-600 text-white px-6 py-4 space-y-3 md:hidden border-t border-pink-500
                                       dark:bg-gray-800 dark:border-gray-700">
            <a href="#kategori" class="block hover:underline py-1">Kategori</a>
            <a href="#keunggulan" class="block hover:underline py-1">Keunggulan</a>
            <a href="#cara-kerja" class="block hover:underline py-1">Cara Kerja</a>
            <a href="#testimoni" class="block hover:underline py-1">Testimoni</a>
            <a href="#faq" class="block hover:underline py-1">FAQ</a>
            <a href="#kontak" class="block hover:underline py-1">Kontak</a>

            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'siswa' || $_SESSION['role'] === 'guru')): ?>
                <a href="index.php?page=riwayat-laporan" class="block hover:underline py-1">Lihat Laporan</a>
                <div class="border-t border-pink-500 pt-4 mt-4 dark:border-gray-600">
                    <div class="flex items-center mb-2">
                        <img src="assets/img/profile/<?= htmlspecialchars($_SESSION['foto_profile'] ?? 'default.png') ?>"
                            alt="Avatar" class="w-8 h-8 rounded-full object-cover mr-2 border border-white" />
                        <p class="font-medium"><?= htmlspecialchars($_SESSION['nama'] ?? 'Pelapor') ?></p>
                    </div>
                    <p class="text-xs text-pink-100 mb-2"><?= htmlspecialchars($_SESSION['role'] ?? '') ?></p>
                    <a href="index.php?page=logout" onclick="return confirm('Apakah Anda yakin ingin logout?')"
                        class="block text-white hover:underline">Logout</a>
                </div>
            <?php else: ?>

            <?php endif; ?>
        </div>
    </header>