// assets/js/DataSanksi.js

document.addEventListener('DOMContentLoaded', function() {
    const editSanksiModal = document.getElementById('editSanksiModal');
    const closeEditModalButton = document.getElementById('closeEditModal');
    const editButtons = document.querySelectorAll('.edit-btn'); // Buttons to open edit modal

    const editIdSanksiInput = document.getElementById('edit_id_sanksi');
    const editLaporanIdSelect = document.getElementById('edit_laporan_id'); // Ini adalah elemen <select>
    const editJenisSanksiSelect = document.getElementById('edit_jenis_sanksi');
    const editDeskripsiInput = document.getElementById('edit_deskripsi');
    const editTanggalMulaiInput = document.getElementById('edit_tanggal_mulai');
    const editTanggalSelesaiInput = document.getElementById('edit_tanggal_selesai');
    // const editDiberikanOlehInput = document.getElementById('edit_diberikan_oleh'); // DIHAPUS

    // Function to show the modal
    function showModal() {
        editSanksiModal.classList.remove('hidden');
    }

    // Function to hide the modal
    function hideModal() {
        editSanksiModal.classList.add('hidden');
    }

    // Event listener for opening the modal
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const laporanId = this.dataset.laporan_id;
            const jenisSanksi = this.dataset.jenis_sanksi;
            const deskripsi = this.dataset.deskripsi;
            const tanggalMulai = this.dataset.tanggal_mulai;
            const tanggalSelesai = this.dataset.tanggal_selesai;
            // const diberikanOleh = this.dataset.diberikan_oleh; // DIHAPUS

            editIdSanksiInput.value = id;
            editLaporanIdSelect.value = laporanId; // Set dropdown value
            editJenisSanksiSelect.value = jenisSanksi;
            editDeskripsiInput.value = deskripsi;
            editTanggalMulaiInput.value = tanggalMulai;
            editTanggalSelesaiInput.value = tanggalSelesai;
            // editDiberikanOlehInput.value = diberikanOleh; // DIHAPUS

            showModal();
        });
    });

    // Event listener for closing the modal
    closeEditModalButton.addEventListener('click', hideModal);

    // Close modal when clicking outside of it
    editSanksiModal.addEventListener('click', function(event) {
        if (event.target === editSanksiModal) {
            hideModal();
        }
    });

    // Initialize Feather Icons (if not already done globally)
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});