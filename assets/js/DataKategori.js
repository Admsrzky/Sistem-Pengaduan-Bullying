document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Re-initialize feather icons for newly loaded content

    const editButtons = document.querySelectorAll('.edit-btn');
    const editKategoriModal = document.getElementById('editKategoriModal'); // Updated modal ID
    const closeEditModalButton = document.getElementById('closeEditModal'); // General close button ID (reused)
    const editIdKategoriInput = document.getElementById('edit_id_kategori'); // Updated input ID
    const editNamaKategoriInput = document.getElementById('edit_nama_kategori'); // Updated input ID

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id; // Get ID from data-id
            const nama = this.dataset.nama; // Get name from data-nama

            editIdKategoriInput.value = id; // Populate hidden ID input in modal
            editNamaKategoriInput.value = nama; // Populate name input in modal
            editKategoriModal.classList.remove('hidden'); // Show the modal
        });
    });

    closeEditModalButton.addEventListener('click', function() {
        editKategoriModal.classList.add('hidden'); // Hide the modal
        // Opsional: Clear input field saat modal ditutup
        editNamaKategoriInput.value = '';
    });

    // Close modal if user clicks outside of it
    editKategoriModal.addEventListener('click', function(event) {
        if (event.target === editKategoriModal) {
            editKategoriModal.classList.add('hidden');
            // Opsional: Clear input field saat modal ditutup
            editNamaKategoriInput.value = '';
        }
    });

    // Handle ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !editKategoriModal.classList.contains('hidden')) {
            editKategoriModal.classList.add('hidden');
            // Opsional: Clear input field saat modal ditutup
            editNamaKategoriInput.value = '';
        }
    });
});