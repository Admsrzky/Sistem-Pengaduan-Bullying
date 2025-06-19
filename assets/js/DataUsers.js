// Move the JS block to its own file 'DataUsers.js' and include it in your footer.php or just below this file.
// For now, I'm putting it directly here for completeness.
document.addEventListener('DOMContentLoaded', function() {
    feather.replace(); // Re-initialize feather icons for newly loaded content

    const editButtons = document.querySelectorAll('.edit-btn');
    const editUserModal = document.getElementById('editUserModal');
    const closeEditModalButton = document.getElementById('closeEditModal');

    const editIdUserInput = document.getElementById('edit_id_user');
    const editNisnipInput = document.getElementById('edit_nisnip');
    const editNamaInput = document.getElementById('edit_nama');
    const editRoleSelect = document.getElementById('edit_role');
    const editNewPasswordInput = document.getElementById('edit_new_password');
    const editFotoProfileInput = document.getElementById('edit_foto_profile');

    // Elements for Guru-specific fields
    const editJabatanIdSelect = document.getElementById('edit_jabatan_id');
    const editJabatanIdField = document.getElementById(
        'edit_jabatan_id_field'); // The div containing Jabatan select

    // Elements for Siswa-specific fields
    const editJurusanIdSelect = document.getElementById('edit_jurusan_id');
    const editKelasIdSelect = document.getElementById('edit_kelas_id');
    const editSiswaFields = document.getElementById('edit_siswa_fields'); // The div containing Siswa fields

    // Hidden fields to store current values for comparison
    const currentNisnipDisplay = document.getElementById('current_nisnip_display');
    const currentNamaDisplay = document.getElementById('current_nama_display');
    const currentRoleDisplay = document.getElementById('current_role_display');
    const currentFotoProfileDisplay = document.getElementById('current_foto_profile_display');
    const currentJabatanIdDisplay = document.getElementById('current_jabatan_id_display');
    const currentJurusanIdDisplay = document.getElementById('current_jurusan_id_display');
    const currentKelasIdDisplay = document.getElementById('current_kelas_id_display');
    const currentFotoPreview = document.getElementById('current_foto_preview');

    // For the Add User form (to initialize its role-specific fields)
    const addRoleSelect = document.getElementById('add_role');
    const addJabatanIdSelect = document.getElementById('add_jabatan_id');
    const addJabatanIdField = document.getElementById('add_jabatan_id_field');
    const addJurusanIdSelect = document.getElementById('add_jurusan_id');
    const addKelasIdSelect = document.getElementById('add_kelas_id');
    const addSiswaFields = document.getElementById('add_siswa_fields');

    // Store all original kelas options (no filtering by jurusan based on your DB schema)
    // This list will contain ALL available classes.
    const allKelasOptions = Array.from(
        document.querySelector('#edit_kelas_id') ? document.querySelector('#edit_kelas_id').options :
        (document.querySelector('#add_kelas_id') ? document.querySelector('#add_kelas_id').options : [])
    ).filter(option => option.value !== '');


    // FIX: Removed filterKelasOptions as 'kelas' does not have 'jurusan_id' directly.
    // The Jurusan and Kelas dropdowns will now be independent selections.

    // Function to dynamically show/hide role-specific fields and manage 'required' attributes
    function toggleRoleSpecificFields(formType = 'edit') {
        let targetRoleSelect;
        let targetJabatanField;
        let targetJabatanSelect;
        let targetSiswaFields;
        let targetJurusanSelect;
        let targetKelasSelect;

        if (formType === 'add') {
            targetRoleSelect = addRoleSelect;
            targetJabatanField = addJabatanIdField;
            targetJabatanSelect = addJabatanIdSelect;
            targetSiswaFields = addSiswaFields;
            targetJurusanSelect = addJurusanIdSelect;
            targetKelasSelect = addKelasIdSelect;
        } else { // 'edit'
            targetRoleSelect = editRoleSelect;
            targetJabatanField = editJabatanIdField;
            targetJabatanSelect = editJabatanIdSelect;
            targetSiswaFields = editSiswaFields;
            targetJurusanSelect = editJurusanIdSelect;
            targetKelasSelect = editKelasIdSelect;
        }

        // Hide all specific fields first and remove 'required'
        targetJabatanField.style.display = 'none';
        targetJabatanSelect.removeAttribute('required');

        targetSiswaFields.style.display = 'none';
        targetJurusanSelect.removeAttribute('required');
        targetKelasSelect.removeAttribute('required');


        // Show fields based on selected role
        if (targetRoleSelect.value === 'guru') {
            targetJabatanField.style.display = 'block';
            targetJabatanSelect.setAttribute('required', 'required');
            // For edit form, restore current value, else clear for add form
            if (formType === 'edit') {
                targetJabatanSelect.value = currentJabatanIdDisplay.value;
            } else {
                targetJabatanSelect.value = '';
            }
            // Clear siswa fields if switching from siswa
            targetJurusanSelect.value = '';
            targetKelasSelect.value = '';

        } else if (targetRoleSelect.value === 'siswa') {
            targetSiswaFields.style.display = 'grid'; // Use grid for layout
            targetJurusanSelect.setAttribute('required', 'required');
            targetKelasSelect.setAttribute('required', 'required');
            // For edit form, restore current values, else clear for add form
            if (formType === 'edit') {
                targetJurusanSelect.value = currentJurusanIdDisplay.value;
                targetKelasSelect.value = currentKelasIdDisplay.value;
            } else {
                targetJurusanSelect.value = '';
                targetKelasSelect.value = '';
            }
            // Clear guru fields if switching from guru
            targetJabatanSelect.value = '';
        } else {
            // For roles like Admin, BK, Kepsesek, clear both
            targetJabatanSelect.value = '';
            targetJurusanSelect.value = '';
            targetKelasSelect.value = '';
        }
    }

    // FIX: Removed event listener for Jurusan select change as kelas is no longer filtered by jurusan.
    // addJurusanIdSelect.addEventListener('change', ...);
    // editJurusanIdSelect.addEventListener('change', ...);


    // --- Event Listeners for Edit Modal ---
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const nisnip = this.dataset.nisnip;
            const nama = this.dataset.nama;
            const role = this.dataset.role;
            const foto = this.dataset.foto;
            const jabatan = this.dataset.jabatan; // For Guru
            const jurusan = this.dataset.jurusan; // For Siswa
            const kelas = this.dataset.kelas; // For Siswa

            editIdUserInput.value = id;
            editNisnipInput.value = nisnip;
            editNamaInput.value = nama;
            editRoleSelect.value = role;

            // Store current values in hidden fields for comparison
            currentNisnipDisplay.value = nisnip;
            currentNamaDisplay.value = nama;
            currentRoleDisplay.value = role;
            currentFotoProfileDisplay.value = foto;
            currentJabatanIdDisplay.value = jabatan;
            currentJurusanIdDisplay.value = jurusan;
            currentKelasIdDisplay.value = kelas;

            // Set current photo preview source
            currentFotoPreview.src = '<?= $asset_base_path_users ?>' + (foto || 'default.png');

            // Set initial dropdown values before calling toggle to ensure they are available
            editJabatanIdSelect.value = jabatan;
            editJurusanIdSelect.value = jurusan;
            editKelasIdSelect.value = kelas;

            toggleRoleSpecificFields(
                'edit'); // Call function to show/hide fields based on initial role
            editUserModal.classList.remove('hidden'); // Show the modal
        });
    });

    closeEditModalButton.addEventListener('click', function() {
        editUserModal.classList.add('hidden'); // Hide the modal
        // Clear inputs and reset dropdowns
        editNewPasswordInput.value = '';
        editFotoProfileInput.value = '';
        // Call toggle with 'edit' to reset based on default role (or clear all)
        toggleRoleSpecificFields('edit');
        editRoleSelect.value = ''; // Reset role dropdown
    });

    // Close modal if user clicks outside of it
    editUserModal.addEventListener('click', function(event) {
        if (event.target === editUserModal) {
            editUserModal.classList.add('hidden');
            editNewPasswordInput.value = '';
            editFotoProfileInput.value = '';
            toggleRoleSpecificFields('edit');
            editRoleSelect.value = '';
        }
    });

    // Handle ESC key to close modal
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !editUserModal.classList.contains('hidden')) {
            editUserModal.classList.add('hidden');
            editNewPasswordInput.value = '';
            editFotoProfileInput.value = '';
            toggleRoleSpecificFields('edit');
            editRoleSelect.value = '';
        }
    });

    // Add event listener for role change in edit modal to dynamically show/hide specific fields
    editRoleSelect.addEventListener('change', function() {
        toggleRoleSpecificFields('edit');
    });

    // Initial call for the Add User form (to set up initial visibility)
    if (addRoleSelect) { // Check if element exists
        addRoleSelect.addEventListener('change', function() {
            toggleRoleSpecificFields('add');
        });
        // Initial setup for add form on page load
        toggleRoleSpecificFields('add');
    }
});