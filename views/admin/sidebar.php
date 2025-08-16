<aside
    class="w-64 bg-white dark:bg-gray-800 shadow-md flex-shrink-0 transition-all duration-300 transform -translate-x-full lg:translate-x-0"
    id="sidebar">
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <a href="dashboard-admin.php" class="flex items-center space-x-2">
                <img src="../../assets/img/logo.png" alt="SIPENG Logo" class="h-8 w-auto">
                <span class="text-2xl font-extrabold text-gray-800 dark:text-white leading-none">SIPENG</span>
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400 leading-none">Dashboard</span>
            </a>
            <button class="text-gray-500 dark:text-gray-400 focus:outline-none lg:hidden p-2"
                id="sidebar-toggle-mobile">
                <i data-feather="x" class="w-6 h-6"></i>
            </button>
        </div>
        <nav class="flex-1 p-4 space-y-2">
            <a href="dashboard-admin.php"
                class="flex items-center px-4 py-2 text-gray-700 bg-gray-200 dark:text-white dark:bg-gray-700 rounded-md">
                <i data-feather="home" class="mr-2"></i> Dashboard
            </a>

            <?php if ($user_role === 'admin'): ?>
            <div>
                <button
                    class="w-full flex items-center justify-between px-4 py-2 text-gray-700 dark:text-white rounded-md focus:outline-none"
                    id="siswa-btn">
                    <span class="flex items-center">
                        <i data-feather="users" class="mr-2"></i> Siswa
                    </span>
                    <i data-feather="chevron-down"></i>
                </button>
                <div class="pl-8 mt-2 space-y-2 hidden" id="siswa-menu">
                    <a href="Data_jurusan.php"
                        class="block text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">Jurusan</a>
                    <a href="Data_kelas.php"
                        class="block text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">Kelas</a>
                    <a href="Data_siswa.php"
                        class="block text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">All
                        Data Siswa</a>
                </div>
            </div>
            <div>
                <button
                    class="w-full flex items-center justify-between px-4 py-2 text-gray-700 dark:text-white rounded-md focus:outline-none"
                    id="guru-btn">
                    <span class="flex items-center">
                        <i data-feather="briefcase" class="mr-2"></i> Guru
                    </span>
                    <i data-feather="chevron-down"></i>
                </button>
                <div class="pl-8 mt-2 space-y-2 hidden" id="guru-menu">
                    <a href="Data_Jabatan.php"
                        class="block text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">Jabatan</a>
                    <a href="Data_Guru.php"
                        class="block text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">All
                        Data Guru</a>
                </div>
            </div>
            <a href="Data_users.php"
                class="flex items-center px-4 py-2 text-gray-700 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                <i data-feather="user-check" class="mr-2"></i> Users
            </a>
            <a href="Data_Kategori.php"
                class="flex items-center px-4 py-2 text-gray-700 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                <i data-feather="folder" class="mr-2"></i> Kategori
            </a>
            <a href="Data_Sanksi.php"
                class="flex items-center px-4 py-2 text-gray-700 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                <i data-feather="alert-octagon" class="mr-2"></i> Sanksi
            </a>
            <a href="Data_Pengaduan.php"
                class="flex items-center px-4 py-2 text-gray-700 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                <i data-feather="archive" class="mr-2"></i> Pengaduan
                <span class="ml-auto text-xs font-semibold text-white bg-red-500 rounded-full px-2">
                    <?= $latest_reports_count ?? 0 ?>
                </span>
            </a>
            <?php endif; ?>

            <?php if ($user_role === 'admin' || $user_role === 'kepsek'): ?>
            <a href="Data_Laporan.php"
                class="flex items-center px-4 py-2 text-gray-700 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">
                <i data-feather="file-text" class="mr-2"></i> Laporan
            </a>
            <?php endif; ?>
        </nav>
        <div class="p-4 border-t dark:border-gray-700">
            <div class="flex items-center">
                <img src="<?= $asset_base_path . htmlspecialchars($user_foto_profile) ?>" alt="User Profile"
                    class="w-10 h-10 rounded-full object-cover" />
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-800 dark:text-white">
                        <?= htmlspecialchars($user_nama) ?>
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        <?= htmlspecialchars(ucfirst($user_role)) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</aside>