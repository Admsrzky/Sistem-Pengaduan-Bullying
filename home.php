<?php include 'layout/header.php'; ?>

<section
    class="container mx-auto px-4 py-16 md:py-24 flex flex-col md:flex-row items-center justify-between text-center md:text-left">
    <div class="md:w-1/2 md:pr-8 mb-10 md:mb-0">
        <h2 class="text-4xl sm:text-5xl lg:text-6xl dark:text-white font-extrabold mb-6 leading-tight">
            Lapor Bullying & Pelecehan Seksual <br class="hidden lg:block" />
            dengan <span class="text-pink-600 dark:text-pink-400">Aman dan Rahasia</span>
        </h2>
        <p class="text-lg sm:text-xl text-gray-700 mb-8 dark:text-gray-300 max-w-lg md:max-w-none mx-auto md:mx-0">
            Platform resmi MAN 1 Cilegon untuk melindungi siswa dari bullying dan pelecehan.
            Laporkan dengan mudah, aman, dan dapatkan tindak lanjut yang cepat.
        </p>
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 justify-center md:justify-start">
            <?php if (isset($_SESSION['role']) && ($_SESSION['role'] === 'siswa' || $_SESSION['role'] === 'guru')): ?>
                <a href="index?page=laporan"
                    class="inline-block bg-pink-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-pink-700 transition-all duration-300 transform hover:scale-105">
                    Laporkan Sekarang
                </a>
            <?php else: ?>
                <a href="login"
                    class="inline-block bg-pink-600 text-white px-8 py-3 rounded-full font-bold shadow-lg hover:bg-pink-700 transition-all duration-300 transform hover:scale-105">
                    Login
                </a>
                <a href="login"
                    class="inline-block border-2 border-pink-600 text-pink-600 px-8 py-3 rounded-full font-bold hover:bg-pink-50 transition-all duration-300 transform hover:scale-105 dark:text-pink-400 dark:border-pink-400 dark:hover:bg-pink-900">
                    Daftar
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="md:w-1/2 md:pl-8 flex justify-center md:justify-end">
        <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?auto=format&fit=crop&w=800&q=80"
            alt="Ilustrasi Anti Bullying" class="rounded-xl shadow-2xl max-w-full h-auto" />
    </div>
</section>

<section id="kategori" class="bg-gray-50 py-16 md:py-20 dark:bg-gray-800">
    <div class="container mx-auto px-4">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12 dark:text-white">Kategori Pelaporan
            <span class="text-pink-600 dark:text-pink-400">Bullying dan pelecehan seksual</span>
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-10 max-w-7xl mx-auto">
            <div
                class="text-center p-6 sm:p-8 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-pink-100 text-pink-600 mx-auto mb-6 shadow-md dark:bg-pink-900 dark:text-pink-300">
                    <i class="fas fa-comment-alt text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Verbal</h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Penghinaan, ejekan, kata-kata kasar, ancaman
                    verbal.</p>
            </div>
            <div
                class="text-center p-6 sm:p-8 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-pink-100 text-pink-600 mx-auto mb-6 shadow-md dark:bg-pink-900 dark:text-pink-300">
                    <i class="fas fa-hand-rock text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Fisik</h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Pemukulan, dorongan, merusak barang, tindakan
                    fisik lainnya.</p>
            </div>
            <div
                class="text-center p-6 sm:p-8 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-pink-100 text-pink-600 mx-auto mb-6 shadow-md dark:bg-pink-900 dark:text-pink-300">
                    <i class="fas fa-user-friends text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Sosial</h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Pengucilan, penyebaran rumor, pengabaian,
                    manipulasi pertemanan.</p>
            </div>
            <div
                class="text-center p-6 sm:p-8 bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 cursor-pointer dark:bg-gray-800 dark:hover:bg-gray-700">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-pink-100 text-pink-600 mx-auto mb-6 shadow-md dark:bg-pink-900 dark:text-pink-300">
                    <i class="fas fa-desktop text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Cyber</h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Pelecehan online, pesan kasar, peretasan,
                    penyebaran hoaks digital.</p>
            </div>
        </div>
    </div>
</section>

<section id="keunggulan" class="py-16 md:py-20 bg-gradient-to-r from-purple-700 via-pink-600 to-pink-500 text-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <h3 class="text-3xl sm:text-4xl font-bold text-center mb-12">Mengapa Memilih Platform Ini?</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10 text-center">
            <div class="p-6 md:p-8 bg-white bg-opacity-20 rounded-xl shadow-lg backdrop-blur-sm">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-white bg-opacity-30 text-white mx-auto mb-6 shadow-md">
                    <i class="fas fa-lock text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2">Keamanan Terjamin</h4>
                <p class="text-base opacity-90">Data pelapor dan laporan dijaga kerahasiaannya dengan sistem keamanan
                    terbaik dan enkripsi data.</p>
            </div>
            <div class="p-6 md:p-8 bg-white bg-opacity-20 rounded-xl shadow-lg backdrop-blur-sm">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-white bg-opacity-30 text-white mx-auto mb-6 shadow-md">
                    <i class="fas fa-bolt text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2">Tindak Lanjut Cepat</h4>
                <p class="text-base opacity-90">Laporan akan diproses dan ditindaklanjuti oleh pihak sekolah dengan
                    cepat, efektif, dan transparan.</p>
            </div>
            <div class="p-6 md:p-8 bg-white bg-opacity-20 rounded-xl shadow-lg backdrop-blur-sm">
                <div
                    class="flex items-center justify-center w-20 h-20 rounded-full bg-white bg-opacity-30 text-white mx-auto mb-6 shadow-md">
                    <i class="fas fa-users text-4xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-2">Mendukung Kesejahteraan</h4>
                <p class="text-base opacity-90">Menciptakan lingkungan sekolah yang aman, nyaman, dan mendukung
                    pertumbuhan mental seluruh siswa.</p>
            </div>
        </div>
    </div>
</section>

<!-- ## Cara Kerja -->
<section id="cara-kerja" class="bg-gray-50 py-16 md:py-20 dark:bg-gray-900">
    <div class="container mx-auto px-4">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12 dark:text-white">Bagaimana Cara <span
                class="text-pink-600 dark:text-pink-400">Melapor?</span></h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
            <div class="text-center p-8 bg-white rounded-xl shadow-lg dark:bg-gray-800">
                <div
                    class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold dark:bg-pink-900 dark:text-pink-300">
                    1</div>
                <h4 class="font-semibold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Masuk/Daftar</h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Akses platform menggunakan akun Anda. Jika belum
                    punya,
                    daftar dengan mudah dan cepat.</p>
            </div>
            <div class="text-center p-8 bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <div
                    class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold dark:bg-pink-900 dark:text-pink-300">
                    2</div>
                <h4 class="font-semibold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Isi Formulir Laporan
                </h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Sampaikan detail kejadian dengan jelas dan
                    lengkap. Anda bisa melapor
                    secara anonim untuk menjaga kerahasiaan.</p>
            </div>
            <div class="text-center p-8 bg-white rounded-lg shadow-lg dark:bg-gray-800">
                <div
                    class="w-16 h-16 bg-pink-100 text-pink-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl font-bold dark:bg-pink-900 dark:text-pink-300">
                    3</div>
                <h4 class="font-semibold text-xl sm:text-2xl mb-2 text-gray-900 dark:text-white">Tindak Lanjut</h4>
                <p class="text-gray-600 text-base dark:text-gray-300">Tim sekolah akan meninjau laporan Anda dengan
                    serius dan mengambil
                    tindakan yang diperlukan untuk menyelesaikan masalah.</p>
            </div>
        </div>
    </div>
</section>

<!-- Sanksi -->
<section id="sanksi-penjelasan" class="bg-white py-16 md:py-20 dark:bg-gray-700">
    <div class="container mx-auto px-4">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12 dark:text-white">Sanksi Pelanggaran
            <span class="text-pink-600 dark:text-pink-400">Kasus Bullying dan Pelecehan Seksual</span>
        </h3>
        <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">

            <div class="p-6 sm:p-8 bg-gray-50 rounded-xl shadow-lg dark:bg-gray-800">
                <div
                    class="flex items-center justify-center w-16 h-16 rounded-full bg-pink-100 text-pink-600 mx-auto mb-6 shadow-md dark:bg-pink-900 dark:text-pink-300">
                    <i class="fas fa-gavel text-3xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-4 text-center text-gray-900 dark:text-white">Jenis Sanksi
                    yang Diberlakukan</h4>
                <h1 class="text-gray-700 font-bold text-md mb-2 dark:text-gray-300">
                    Sanksi Berat <br><span class="font-normal text-sm ml-4">Tindakan di bawah ini termasuk pelanggaran
                        dengan sanksi berat, dan akan dikenakan sanksi berupa konseling, pemanggilan orang tua, point,
                        bahkan bisa masuk ke pihak yang berwajib.</span>
                </h1>
                <ul class="text-gray-700 text-sm text-left list-decimal mb-2 list-inside space-y-2 dark:text-gray-300">
                    <li>Mencium</li>
                    <li>Mengancam dengan tindakan seksual.</li>
                    <li>Mengintip dengan sengaja.</li>
                    <li>Berhubungan</li>
                    <li>Memukul.</li>
                </ul>
                <h1 class="text-gray-700 font-bold text-md mb-2 dark:text-gray-300">
                    Sanksi Ringan <br><span class="font-normal text-sm ml-4">Tindakan di bawah ini termasuk pelanggaran
                        dengan sanksi ringan, dan akan dikenakan sanksi berupa konseling dan point.</span>
                </h1>
                <ul class="text-gray-700 text-sm text-left list-decimal mb-2 list-inside space-y-2 dark:text-gray-300">
                    <li>Mengomentari Fisik Seseorang</li>
                    <li>Menyebarkan gosip atau rumor seseorang</li>
                    <li>Cat Calling</li>
                    <li>Mencubit</li>
                    <li>Menepuk.</li>
                    <li>Meledek nama orang tua.</li>
                </ul>
            </div>

            <div class="p-6 sm:p-8 bg-gray-50 rounded-xl shadow-lg dark:bg-gray-800">
                <div
                    class="flex items-center justify-center w-16 h-16 rounded-full bg-pink-100 text-pink-600 mx-auto mb-6 shadow-md dark:bg-pink-900 dark:text-pink-300">
                    <i class="fas fa-user-shield text-3xl"></i>
                </div>
                <h4 class="font-bold text-xl sm:text-2xl mb-4 text-center text-gray-900 dark:text-white">Proses
                    Penentuan dan Penanggung Jawab Sanksi</h4>
                <p class="text-gray-700 text-base mb-4 dark:text-gray-300">
                    Setiap laporan yang masuk akan ditindaklanjuti dengan serius. Proses penentuan sanksi melibatkan
                    investigasi menyeluruh untuk memastikan keadilan bagi semua pihak.
                </p>
                <ul class="text-gray-700 text-base text-left list-decimal list-inside space-y-2 dark:text-gray-300">
                    <li>Pihak yang Disanksi: Sanksi akan diberikan kepada pelaku/pembuli yang terbukti melakukan
                        pelanggaran.</li>
                    <li>Penentu Sanksi: Penentuan jenis dan beratnya sanksi sepenuhnya menjadi wewenang Guru
                        Bimbingan Konseling (BK) sebagai bagian dari manajemen sekolah yang berwenang.</li>
                    <li>Tujuan Sanksi: Sanksi bertujuan untuk memberikan efek jera, mendidik pelaku agar tidak
                        mengulangi perbuatannya, serta menciptakan lingkungan sekolah yang aman dan nyaman bagi seluruh
                        warga sekolah.</li>
                    <li>Kerja Sama: Proses ini juga melibatkan koordinasi dengan orang tua/wali siswa untuk
                        memastikan pembinaan yang berkelanjutan.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- ## Testimoni -->
<section id="testimoni" class="py-16 md:py-20 bg-white dark:bg-gray-800">
    <div class="container mx-auto px-4 max-w-4xl">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12 dark:text-white">Apa Kata <span
                class="text-pink-600 dark:text-pink-400">Mereka?</span></h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 lg:gap-10">
            <div class="bg-gray-50 p-8 rounded-xl shadow-lg dark:bg-gray-700">
                <p class="text-lg italic text-gray-700 mb-4 dark:text-gray-200">
                    "Saya sangat bersyukur ada platform ini. Melapor jadi lebih mudah dan saya merasa didengarkan.
                    Respon dari sekolah juga sangat cepat dan efektif!"
                </p>
                <div class="flex items-center mt-6">
                    <img src="https://i.pravatar.cc/48?img=4" alt="Siswa"
                        class="w-12 h-12 rounded-full mr-4 object-cover">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">Siswa Kelas X</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">MAN 1 Cilegon</p>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 p-8 rounded-xl shadow-lg dark:bg-gray-700">
                <p class="text-lg italic text-gray-700 mb-4 dark:text-gray-200">
                    "Sebagai guru BK, platform ini sangat membantu kami menerima laporan bullying secara terorganisir
                    dan menjaga kerahasiaan pelapor. Ini langkah maju yang besar bagi sekolah kami."
                </p>
                <div class="flex items-center mt-6">
                    <img src="https://i.pravatar.cc/48?img=15" alt="Ibu Fitri"
                        class="w-12 h-12 rounded-full mr-4 object-cover">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white">Ibu Fitri</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Guru BK</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ## Pertanyaan Umum -->
<section id="faq" class="bg-gray-50 py-16 md:py-20 dark:bg-gray-900">
    <div class="container mx-auto px-4 max-w-4xl">
        <h3 class="text-3xl sm:text-4xl font-bold text-center text-gray-900 mb-12 dark:text-white">Pertanyaan Umum <span
                class="text-pink-600 dark:text-pink-400">(FAQ)</span></h3>
        <div class="space-y-4">
            <div class="bg-white p-6 rounded-xl shadow-lg dark:bg-gray-800">
                <details class="group">
                    <summary
                        class="flex justify-between items-center cursor-pointer text-gray-900 dark:text-white font-semibold text-lg">
                        Apakah laporan saya akan rahasia?
                        <span class="ml-auto transition-transform duration-300 group-open:rotate-180">
                            <i data-feather="chevron-down"></i>
                        </span>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-300 mt-4">
                        Ya, semua laporan yang masuk melalui platform ini dijamin kerahasiaannya. Anda bahkan bisa
                        memilih untuk melapor secara anonim tanpa mengungkapkan identitas Anda.
                    </p>
                </details>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg dark:bg-gray-800">
                <details class="group">
                    <summary
                        class="flex justify-between items-center cursor-pointer text-gray-900 dark:text-white font-semibold text-lg">
                        Siapa yang akan menindaklanjuti laporan saya?
                        <span class="ml-auto transition-transform duration-300 group-open:rotate-180">
                            <i data-feather="chevron-down"></i>
                        </span>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-300 mt-4">
                        Laporan Anda akan ditindaklanjuti oleh tim khusus yang berdedikasi, terdiri dari guru Bimbingan
                        Konseling (BK) dan pihak terkait di MAN 1 Cilegon yang terlatih dalam menangani kasus bullying
                        dan pelecehan.
                    </p>
                </details>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg dark:bg-gray-800">
                <details class="group">
                    <summary
                        class="flex justify-between items-center cursor-pointer text-gray-900 dark:text-white font-semibold text-lg">
                        Berapa lama waktu yang dibutuhkan untuk tindak lanjut?
                        <span class="ml-auto transition-transform duration-300 group-open:rotate-180">
                            <i data-feather="chevron-down"></i>
                        </span>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-300 mt-4">
                        Kami berkomitmen untuk meninjau dan memulai tindak lanjut laporan dalam waktu 1-3 hari kerja
                        setelah laporan diterima, tergantung pada kompleksitas dan urgensi kasus.
                    </p>
                </details>
            </div>
            <div class="bg-white p-6 rounded-xl shadow-lg dark:bg-gray-800">
                <details class="group">
                    <summary
                        class="flex justify-between items-center cursor-pointer text-gray-900 dark:text-white font-semibold text-lg">
                        Apakah saya bisa melapor jika bukan siswa MAN 1 Cilegon?
                        <span class="ml-auto transition-transform duration-300 group-open:rotate-180">
                            <i data-feather="chevron-down"></i>
                        </span>
                    </summary>
                    <p class="text-gray-600 dark:text-gray-300 mt-4">
                        Platform ini dirancang khusus untuk siswa dan komunitas internal MAN 1 Cilegon. Jika Anda bukan
                        bagian dari sekolah, harap hubungi pihak berwenang atau layanan dukungan yang relevan di wilayah
                        Anda.
                    </p>
                </details>
            </div>
        </div>
    </div>
</section>

<?php include 'layout/footer.php'; ?>