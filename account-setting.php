<?php
include 'layout/header.php';
include 'config/database.php';

// Redirect jika belum login
if (!isset($_SESSION['nis_nip'])) {
    header('Location: login.php');
    exit();
}

// Ambil data user
$nis_nip = mysqli_real_escape_string($conn, $_SESSION['nis_nip']);
$query = mysqli_query($conn, "SELECT * FROM users WHERE nis_nip = '$nis_nip'");
$user = mysqli_fetch_assoc($query);
?>

<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-xl mx-auto bg-white p-10 rounded-xl shadow-lg">
        <h2 class="text-2xl font-bold text-center text-pink-600 mb-6">Ubah Password</h2>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 mb-4 rounded">
            <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
        </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 mb-4 rounded">
            <?= $_SESSION['success'];
                unset($_SESSION['success']); ?>
        </div>
        <?php endif; ?>

        <form action="update-password.php" method="POST" class="space-y-6">
            <div>
                <label class="block font-semibold text-gray-700 mb-1">Password Lama</label>
                <input type="password" name="old_password" required
                    class="w-full px-4 text-black py-2 border rounded-md focus:ring-2 focus:ring-pink-400 shadow-sm">
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Password Baru</label>
                <input type="password" name="new_password" required minlength="6"
                    class="w-full px-4 text-black py-2 border rounded-md focus:ring-2 focus:ring-pink-400 shadow-sm">
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Konfirmasi Password Baru</label>
                <input type="password" name="confirm_password" required minlength="6"
                    class="w-full px-4 py-2 text-black border rounded-md focus:ring-2 focus:ring-pink-400 shadow-sm">
            </div>

            <div class="text-center pt-4">
                <button type="submit"
                    class="bg-pink-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-pink-700 transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<?php include 'layout/footer.php'; ?>