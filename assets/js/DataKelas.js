document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Re-initialize feather icons for newly loaded content

    const editButtons = document.querySelectorAll('.edit-btn');
    const editKelasModal = document.getElementById('editKelasModal');
    const closeEditModalButton = document.getElementById('closeEditModal');
    const editIdKelasInput = document.getElementById('edit_id_kelas');
    const editNamaKelasInput = document.getElementById('edit_nama_kelas');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id; // Get ID from data-id
            const nama = this.dataset.nama;

            editIdKelasInput.value = id; // Fill hidden ID input in modal
            editNamaKelasInput.value = nama;
            editKelasModal.classList.remove('hidden'); // Show the modal
        });
    });

    closeEditModalButton.addEventListener('click', function() {
        editKelasModal.classList.add('hidden'); // Hide the modal
    });

    // Close modal if user clicks outside of it
    editKelasModal.addEventListener('click', function(event) {
        if (event.target === editKelasModal) {
            editKelasModal.classList.add('hidden');
        }
    });

    // Handle ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !editKelasModal.classList.contains('hidden')) {
            editKelasModal.classList.add('hidden');
        }
    });
});