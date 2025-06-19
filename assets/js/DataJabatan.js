document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Re-initialize feather icons for newly loaded content

    const editButtons = document.querySelectorAll('.edit-btn');
    const editJabatanModal = document.getElementById('editJabatanModal'); // Updated modal ID
    const closeEditModalButton = document.getElementById('closeEditModal'); // General close button ID (reused)
    const editIdJabatanInput = document.getElementById('edit_id_jabatan'); // Updated input ID
    const editNamaJabatanInput = document.getElementById('edit_nama_jabatan'); // Updated input ID

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id; // Get ID from data-id
            const nama = this.dataset.nama; // Get name from data-nama

            editIdJabatanInput.value = id; // Populate hidden ID input in modal
            editNamaJabatanInput.value = nama; // Populate name input in modal
            editJabatanModal.classList.remove('hidden'); // Show the modal
        });
    });

    closeEditModalButton.addEventListener('click', function() {
        editJabatanModal.classList.add('hidden'); // Hide the modal
        // Optional: Clear input field saat modal ditutup
        editNamaJabatanInput.value = ''; 
    });

    // Close modal if user clicks outside of it
    editJabatanModal.addEventListener('click', function(event) {
        if (event.target === editJabatanModal) {
            editJabatanModal.classList.add('hidden');
            // Optional: Clear input field saat modal ditutup
            editNamaJabatanInput.value = '';
        }
    });

    // Handle ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !editJabatanModal.classList.contains('hidden')) {
            editJabatanModal.classList.add('hidden');
            // Optional: Clear input field saat modal ditutup
            editNamaJabatanInput.value = '';
        }
    });
});