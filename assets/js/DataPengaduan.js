    document.addEventListener('DOMContentLoaded', function() {
        feather.replace(); // Re-initialize feather icons

        const editButtons = document.querySelectorAll('.edit-btn');
        const editLaporanModal = document.getElementById('editLaporanModal');
        const closeEditModalButton = document.getElementById('closeEditModal');

        const editLaporanIdInput = document.getElementById('edit_laporan_id');
        const editKategoriIdSelect = document.getElementById('edit_kategori_id');
        const editKronologiTextarea = document.getElementById('edit_kronologi');
        const editLokasiInput = document.getElementById('edit_lokasi');
        const editTanggalKejadianInput = document.getElementById('edit_tanggal_kejadian');
        const editStatusSelect = document.getElementById('edit_status');
        const editBuktiFileInput = document.getElementById('edit_bukti_file');
        const currentBuktiFileHidden = document.getElementById('current_bukti_file');
        const currentBuktiDisplay = document.getElementById('current_bukti_display');
        const currentBuktiPreviewContainer = document.getElementById('current_bukti_preview_container');
        const clearBuktiFileCheckbox = document.getElementById('clear_bukti_file_checkbox');


        // Hidden fields for comparison (populated with original values)
        const currentKategoriIdDisplay = document.getElementById('current_kategori_id_display');
        const currentKronologiDisplay = document.getElementById('current_kronologi_display');
        const currentLokasiDisplay = document.getElementById('current_lokasi_display');
        const currentTanggalKejadianDisplay = document.getElementById('current_tanggal_kejadian_display');
        const currentStatusDisplay = document.getElementById('current_status_display');


        editButtons.forEach(button => {
            button.addEventListener('click', function() {
                const id = this.dataset.id;
                const kategoriId = this.dataset.kategoriId;
                const kronologi = this.dataset.kronologi;
                const lokasi = this.dataset.lokasi;
                const tanggalKejadian = this.dataset.tanggalKejadian; // Format YYYY-MM-DD
                const bukti = this.dataset.bukti; // This will be the filename
                const status = this.dataset.status;

                // Populate form fields
                editLaporanIdInput.value = id;
                editKategoriIdSelect.value = kategoriId;
                editKronologiTextarea.value = kronologi;
                editLokasiInput.value = lokasi;
                editTanggalKejadianInput.value =
                    tanggalKejadian; // HTML date input expects YYYY-MM-DD
                editStatusSelect.value = status;

                // Store current values in hidden fields for comparison
                currentKategoriIdDisplay.value = kategoriId;
                currentKronologiDisplay.value = kronologi;
                currentLokasiDisplay.value = lokasi;
                currentTanggalKejadianDisplay.value = tanggalKejadian;
                currentBuktiFileHidden.value = bukti; // Store filename for backend
                currentStatusDisplay.value = status;


                // Display current bukti filename in the edit modal (no live preview)
                if (bukti) {
                    // Construct the full web path for the current bukti file
                    const fullBuktiWebPath = '<?= $asset_base_path_bukti_web ?>' + bukti;
                    currentBuktiDisplay.innerHTML =
                        `<a href="${fullBuktiWebPath}" target="_blank">${bukti}</a>`;
                    currentBuktiPreviewContainer.style.display = 'block';
                    clearBuktiFileCheckbox.checked =
                        false; // Uncheck clear checkbox when displaying existing bukti
                } else {
                    currentBuktiDisplay.innerHTML = 'N/A';
                    currentBuktiPreviewContainer.style.display =
                        'block'; // Keep container visible even if N/A
                    clearBuktiFileCheckbox.checked = false;
                }

                editLaporanModal.classList.remove('hidden');
            });
        });

        closeEditModalButton.addEventListener('click', function() {
            editLaporanModal.classList.add('hidden');
            // Clear form fields and reset previews/checkboxes
            editKronologiTextarea.value = '';
            editLokasiInput.value = '';
            editTanggalKejadianInput.value = '';
            editBuktiFileInput.value = ''; // Clear file input
            currentBuktiDisplay.innerHTML = 'N/A';
            currentBuktiFileHidden.value = '';
            currentBuktiPreviewContainer.style.display = 'none'; // Hide preview if no file
            clearBuktiFileCheckbox.checked = false;
            editKategoriIdSelect.value = '';
            editStatusSelect.value = '';
        });

        // Close modal if user clicks outside of it
        editLaporanModal.addEventListener('click', function(event) {
            if (event.target === editLaporanModal) {
                closeEditModalButton.click(); // Reuse click handler
            }
        });

        // Handle ESC key to close modal
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !editLaporanModal.classList.contains('hidden')) {
                closeEditModalButton.click(); // Reuse click handler
            }
        });

        // Optional: Reset clear bukti checkbox if a new file is chosen
        editBuktiFileInput.addEventListener('change', function() {
            if (this.value) { // If a new file is selected
                clearBuktiFileCheckbox.checked = false; // Ensure "clear" is not checked
            }
        });


        // --- NEW: View Sanksi Modal Logic ---
        const viewSanksiModal = document.getElementById('viewSanksiModal');
        const closeViewSanksiModalButton = document.getElementById('closeViewSanksiModal');
        const sanksiDetailsContent = document.getElementById('sanksi_details_content');
        const sanksiLaporanIdDisplay = document.getElementById('sanksi_laporan_id_display');
        const viewSanksiButtons = document.querySelectorAll('.view-sanksi-btn');

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
                            <p class="font-semibold text-teal-600 dark:text-teal-400">${sanksi.jenis_sanksi}</p>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Deskripsi: ${sanksi.deskripsi}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Durasi: ${sanksi.tanggal_mulai} ${sanksi.tanggal_selesai ? 'sampai ' + sanksi.tanggal_selesai : ''}
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

        // --- Simple Lightbox Initialization ---
        // Initialize SimpleLightbox only on elements with the data-lightbox attribute
        var lightbox = new SimpleLightbox('.min-w-full a[data-lightbox]', {
            // Add any options you want here, e.g.:
            captionsData: 'title', // use data-title for captions
            captionDelay: 0,
            animationSlide: false
        });
    });