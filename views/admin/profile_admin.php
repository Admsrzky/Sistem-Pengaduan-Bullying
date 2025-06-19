<?php

include 'header.php'; // Pastikan session_start() ada di sini dan $user_id, $user_nama, $user_foto_profile sudah disetel.

include '../../controllers/ProfileController.php'; // Pastikan path ini benar sesuai struktur direktori Anda
?>

<main class="flex-1 p-6 overflow-y-auto">
    <h2 class="text-3xl font-bold text-gray-800 dark:text-white mb-6">
        Edit Profile
    </h2>

    <?php if ($success_message): ?>
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Success!</strong>
        <span class="block sm:inline"><?= $success_message ?></span>
    </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Error!</strong>
        <span class="block sm:inline"><?= $error_message ?></span>
    </div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="mb-6 flex flex-col items-center">
                <label for="foto_profile" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Profile Picture
                </label>
                <div class="profile-img-container">
                    <img id="profile-preview" src="<?= $asset_base_path . htmlspecialchars($user_foto_profile) ?>"
                        alt="Profile Picture"
                        class="w-32 h-32 rounded-full object-cover border-4 border-blue-300 dark:border-blue-700">
                    <input type="file" id="foto_profile" name="foto_profile" accept="image/*" class="hidden">
                    <div
                        class="profile-img-overlay absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full cursor-pointer opacity-0 hover:opacity-100 transition-opacity duration-300">
                        <i data-feather="camera" class="text-white text-3xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Klik untuk mengubah. Ukuran maks: 5MB.
                    Format: JPG, JPEG, PNG, GIF.</p>
            </div>

            <div class="mb-4">
                <label for="nama" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Name
                </label>
                <input type="text" id="nama" name="nama" value="<?= htmlspecialchars($user_nama) ?>"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Your Name" required>
            </div>

            <div class="mb-4">
                <label for="current_password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Current Password (hanya jika ingin mengubah password)
                </label>
                <input type="password" id="current_password" name="current_password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Masukkan password saat ini">
            </div>

            <div class="mb-4">
                <label for="new_password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    New Password (biarkan kosong jika tidak mengubah)
                </label>
                <input type="password" id="new_password" name="new_password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Masukkan password baru">
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 dark:text-gray-300 text-sm font-bold mb-2">
                    Confirm New Password
                </label>
                <input type="password" id="confirm_password" name="confirm_password"
                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline dark:bg-gray-700 dark:text-white dark:border-gray-600"
                    placeholder="Konfirmasi password baru">
            </div>

            <div class="flex items-center justify-between">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileImgContainer = document.querySelector('.profile-img-container');
    const fotoProfileInput = document.getElementById('foto_profile');
    const profilePreview = document.getElementById('profile-preview');

    if (profileImgContainer && fotoProfileInput && profilePreview) {
        // Trigger file input click when the image container is clicked
        profileImgContainer.addEventListener('click', function() {
            fotoProfileInput.click();
        });

        // Display image preview when a file is selected
        fotoProfileInput.addEventListener('change', function(event) {
            const file = event.target.files[0]; // Get the selected file

            if (file) {
                const reader = new FileReader(); // Create a FileReader object

                reader.onload = function(e) {
                    profilePreview.src = e.target.result; // Set the image source to the preview
                };

                reader.readAsDataURL(file); // Read the file as a data URL
            }
        });
    }

    // Optional: Feather icons initialization if you're using it
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
});
</script>

<?php include 'footer.php'; ?>