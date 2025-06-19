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

<footer id="kontak" class="bg-pink-600 text-white py-12 md:py-16 dark:bg-gray-800">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-12 text-center md:text-left">

            <div>
                <a href="#" class="inline-block text-2xl font-bold mb-4">SIPENG <span class="text-pink-200">Anti
                        Bullying</span></a>
                <p class="text-pink-100 text-sm md:text-base leading-relaxed">
                    Platform pelaporan bullying dan pelecehan seksual di MAN 1 Cilegon. Menjamin keamanan dan
                    kerahasiaan untuk lingkungan sekolah yang lebih baik.
                </p>
            </div>

            <div>
                <h5 class="font-semibold text-lg mb-4">Tautan Cepat</h5>
                <ul class="space-y-2">
                    <li><a href="#kategori" class="text-pink-100 hover:text-white transition duration-300">Kategori</a>
                    </li>
                    <li><a href="#keunggulan"
                            class="text-pink-100 hover:text-white transition duration-300">Keunggulan</a></li>
                    <li><a href="#cara-kerja" class="text-pink-100 hover:text-white transition duration-300">Cara
                            Kerja</a></li>
                    <li><a href="#testimoni"
                            class="text-pink-100 hover:text-white transition duration-300">Testimoni</a></li>
                    <li><a href="#faq" class="text-pink-100 hover:text-white transition duration-300">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h5 class="font-semibold text-lg mb-4">Hubungi Kami</h5>
                <p class="text-pink-100 text-sm md:text-base mb-2">
                    <i class="fas fa-map-marker-alt mr-2"></i> Jl. Jend. Sudirman No. 123, Cilegon, Banten
                </p>
                <p class="text-pink-100 text-sm md:text-base mb-2">
                    <i class="fas fa-phone-alt mr-2"></i> (0254) 1234567
                </p>
                <p class="text-pink-100 text-sm md:text-base">
                    <i class="fas fa-envelope mr-2"></i> info@man1cilegon.sch.id
                </p>
                <div class="flex justify-center md:justify-start space-x-4 mt-6">
                    <a href="#" class="text-pink-100 hover:text-white transition duration-300"><i
                            class="fab fa-facebook-f text-xl"></i></a>
                    <a href="#" class="text-pink-100 hover:text-white transition duration-300"><i
                            class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="text-pink-100 hover:text-white transition duration-300"><i
                            class="fab fa-instagram text-xl"></i></a>
                    <a href="#" class="text-pink-100 hover:text-white transition duration-300"><i
                            class="fab fa-youtube text-xl"></i></a>
                </div>
            </div>

        </div>

        <div class="border-t border-pink-500 border-opacity-50 mt-12 pt-8 text-center">
            <p class="text-pink-200 text-sm md:text-base">&copy; 2025 MAN 1 Cilegon. All rights reserved.</p>
            <p class="text-pink-200 text-xs mt-1">Dibuat dengan ❤️ oleh Tim Dev MAN 1 Cilegon</p>
        </div>
    </div>
</footer>

<script src="./assets/js/app.js"></script>

</body>

</html>