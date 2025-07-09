 // --- Simple Lightbox Initialization for Images ---
    var lightbox = new SimpleLightbox('.min-w-full a[data-lightbox]', {
        captionsData: 'title', // use data-title for captions
        captionDelay: 0,
        animationSlide: false
    });


    // --- Custom Video Modal Logic ---
    const videoModal = document.getElementById('videoModal');
    const closeVideoModalBtn = document.getElementById('closeVideoModal');
    const videoPlayer = document.getElementById('videoPlayer');
    const videoModalTitle = document.getElementById('videoModalTitle');
    const viewVideoButtons = document.querySelectorAll('.view-video-btn');

    viewVideoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const videoSrc = this.dataset.videoSrc;
            const title = this.dataset.title;

            videoModalTitle.textContent = title;
            videoPlayer.src = videoSrc;
            videoModal.classList.remove('hidden');
            videoPlayer.play(); // Auto-play the video when modal opens
        });
    });

    closeVideoModalBtn.addEventListener('click', function() {
        videoPlayer.pause(); // Pause video when closing
        videoPlayer.currentTime = 0; // Reset video to start
        videoModal.classList.add('hidden');
    });

    // Close video modal if user clicks outside of it
    videoModal.addEventListener('click', function(event) {
        if (event.target === videoModal) {
            closeVideoModalBtn.click();
        }
    });

    // Handle ESC key to close video modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !videoModal.classList.contains('hidden')) {
            closeVideoModalBtn.click();
        }
    });


    // --- NEW: View Sanksi Modal Logic ---
    const viewSanksiModal = document.getElementById('viewSanksiModal');
    const closeViewSanksiModalButton = document.getElementById('closeViewSanksiModal');
    const sanksiDetailsContent = document.getElementById('sanksi_details_content');
    const sanksiLaporanIdDisplay = document.getElementById('sanksi_laporan_id_display');
    const viewSanksiButtons = document.querySelectorAll('.view-sanksi-btn'); // Select all buttons with this class

    viewSanksiButtons.forEach(button => {
        button.addEventListener('click', function() {
            const laporanId = this.dataset.laporanId;
            const sanksiDataJson = this.dataset.sanksiData;
            const sanksiData = JSON.parse(sanksiDataJson); // Parse JSON string into JS array

            sanksiLaporanIdDisplay.textContent = laporanId; // Set laporan ID in modal title

            // Build HTML content for sanksi details
            let htmlContent = '';
            if (sanksiData && sanksiData.length > 0) {
                sanksiData.forEach(sanksi => {
                    htmlContent += `
                    <div class="mb-4 p-3 border rounded border-gray-200 dark:border-gray-700">
                        <p class="font-semibold text-teal-600 dark:text-teal-400">Jenis Sanksi: ${sanksi.jenis_sanksi}</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">Deskripsi: ${sanksi.deskripsi}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Durasi: ${sanksi.tanggal_mulai} ${sanksi.tanggal_selesai ? 'sampai ' + sanksi.tanggal_selesai : '(Tidak ada tanggal selesai)'}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Diberikan Oleh: ${sanksi.diberikan_oleh}</p>
                    </div>
                `;
                });
            } else {
                htmlContent =
                    '<p class="text-center text-gray-500 dark:text-gray-400">Tidak ada sanksi yang terkait dengan laporan ini.</p>';
            }
            sanksiDetailsContent.innerHTML = htmlContent;

            viewSanksiModal.classList.remove('hidden'); // Show the modal
        });
    });

    closeViewSanksiModalButton.addEventListener('click', function() {
        viewSanksiModal.classList.add('hidden'); // Hide the modal
        sanksiDetailsContent.innerHTML = ''; // Clear content
        sanksiLaporanIdDisplay.textContent = ''; // Clear ID
    });

    // Close view modal if user clicks outside of it
    viewSanksiModal.addEventListener('click', function(event) {
        if (event.target === viewSanksiModal) {
            closeViewSanksiModalButton.click();
        }
    });

    // Handle ESC key to close view modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !viewSanksiModal.classList.contains('hidden')) {
            closeViewSanksiModalButton.click();
        }
    });

    // --- Existing Edit Laporan Modal Logic (from your provided code) ---
    // This part remains unchanged as it was already correct for the edit modal
    document.addEventListener('DOMContentLoaded', function() {
        // ... (Your existing edit modal JS code here, ensure no conflicts with new code above)
        // Removed duplicate DOMContentLoaded wrapper for clarity, this content is now inside the main one.
    });