<?php
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Sukses Melaporkan dengan Efek Meriah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Animasi ceklis berkali-kali: fade in & fade out */
        @keyframes checkmark-blink {

            0%,
            20%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            10% {
                opacity: 0;
                transform: scale(0.8);
            }
        }

        .animate-checkmark-blink {
            animation: checkmark-blink 1.5s ease-in-out infinite;
        }

        /* Konfeti Styles */
        .confetti-piece {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            opacity: 0.9;
            animation-timing-function: ease-in-out;
            animation-iteration-count: infinite;
        }

        /* Variasi warna konfeti */
        .confetti-1 {
            background-color: #fbbf24;
            /* Yellow */
            animation-name: confetti-fall-1;
            animation-duration: 3s;
            animation-delay: 0s;
        }

        .confetti-2 {
            background-color: #ef4444;
            /* Red */
            animation-name: confetti-fall-2;
            animation-duration: 2.5s;
            animation-delay: 0.3s;
        }

        .confetti-3 {
            background-color: #3b82f6;
            /* Blue */
            animation-name: confetti-fall-3;
            animation-duration: 3.5s;
            animation-delay: 0.7s;
        }

        .confetti-4 {
            background-color: #10b981;
            /* Green */
            animation-name: confetti-fall-4;
            animation-duration: 2.7s;
            animation-delay: 0.4s;
        }

        .confetti-5 {
            background-color: #ec4899;
            /* Pink */
            animation-name: confetti-fall-5;
            animation-duration: 3.2s;
            animation-delay: 0.6s;
        }

        /* Keyframes jatuh konfeti dengan rotasi dan opacity */
        @keyframes confetti-fall-1 {
            0% {
                opacity: 0.9;
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                opacity: 0;
                transform: translate(30px, 100px) rotate(360deg);
            }
        }

        @keyframes confetti-fall-2 {
            0% {
                opacity: 0.9;
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                opacity: 0;
                transform: translate(-25px, 110px) rotate(-360deg);
            }
        }

        @keyframes confetti-fall-3 {
            0% {
                opacity: 0.9;
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                opacity: 0;
                transform: translate(20px, 120px) rotate(180deg);
            }
        }

        @keyframes confetti-fall-4 {
            0% {
                opacity: 0.9;
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                opacity: 0;
                transform: translate(-20px, 105px) rotate(270deg);
            }
        }

        @keyframes confetti-fall-5 {
            0% {
                opacity: 0.9;
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                opacity: 0;
                transform: translate(15px, 115px) rotate(-180deg);
            }
        }

        /* Container relatif ikon centang agar konfeti posisi relatif ke ikon */
        .icon-wrapper {
            position: relative;
            width: 80px;
            height: 80px;
        }
    </style>
</head>

<body class="bg-white min-h-screen flex flex-col justify-center items-center p-6 overflow-hidden">

    <div class="flex flex-col items-center gap-6 max-w-xs w-full text-center relative z-10">
        <!-- Icon centang dengan animasi berkali-kali -->
        <div class="icon-wrapper mx-auto">
            <div class="bg-teal-400 rounded-full w-20 h-20 flex items-center justify-center shadow-lg">
                <svg class="w-12 h-12 text-white animate-checkmark-blink" fill="none" stroke="currentColor"
                    stroke-width="3" viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5" />
                </svg>
            </div>

            <!-- Konfeti muncul dari ikon -->
            <div class="confetti-piece confetti-1" style="top: 15%; left: 10%;"></div>
            <div class="confetti-piece confetti-2" style="top: 10%; left: 40%;"></div>
            <div class="confetti-piece confetti-3" style="top: 5%; left: 70%;"></div>
            <div class="confetti-piece confetti-4" style="top: 20%; left: 55%;"></div>
            <div class="confetti-piece confetti-5" style="top: 18%; left: 25%;"></div>
        </div>

        <!-- Judul -->
        <h1 class="text-lg font-bold text-gray-900">
            Yeay! Laporan kamu berhasil dibuat
        </h1>

        <!-- Deskripsi -->
        <p class="text-gray-700">
            Kamu bisa melihat laporan yang dibuat di halaman laporan
        </p>

        <!-- Tombol Lihat Laporan -->
        <button type="button"
            class="bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 text-white font-semibold rounded-full px-6 py-2 transition"
            onclick="window.location.href='riwayat-laporan.php'">
            Lihat Laporan
        </button>
    </div>

</body>

</html>