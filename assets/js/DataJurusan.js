document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Re-initialize feather icons for newly loaded content

    const editButtons = document.querySelectorAll('.edit-btn');
    const editJurusanModal = document.getElementById('editJurusanModal');
    const closeEditModalButton = document.getElementById('closeEditModal');
    const editIdJurusanInput = document.getElementById('edit_id_jurusan');
    const editNamaJurusanInput = document.getElementById('edit_nama_jurusan');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id; // Mengambil ID dari data-id
            const nama = this.dataset.nama;

            editIdJurusanInput.value = id; // Mengisi input hidden ID modal
            editNamaJurusanInput.value = nama;
            editJurusanModal.classList.remove('hidden'); // Show the modal
        });
    });

    closeEditModalButton.addEventListener('click', function() {
        editJurusanModal.classList.add('hidden'); // Hide the modal
    });

    // Close modal if user clicks outside of it
    editJurusanModal.addEventListener('click', function(event) {
        if (event.target === editJurusanModal) {
            editJurusanModal.classList.add('hidden');
        }
    });

    // Handle ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !editJurusanModal.classList.contains('hidden')) {
            editJurusanModal.classList.add('hidden');
        }
    });
});