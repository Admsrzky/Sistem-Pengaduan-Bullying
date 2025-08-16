<?php
include '../../config/database.php';
/**
 * =================================================================
 * SanksiController.php
 * (Menggunakan Notifikasi via URL & Pola PRG)
 * =================================================================
 */

// Asumsikan koneksi '$conn' sudah tersedia dari file yang meng-include controller ini.

// --- Handle Form Submissions (Add/Edit/Delete) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($conn)) {
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=error&msg=' . urlencode("Koneksi database tidak ditemukan."));
        exit();
    }

    $action = $_POST['action'] ?? '';
    $status = 'error'; // Default status
    $msg = 'Aksi tidak diketahui atau terjadi kesalahan.'; // Default message

    // Menggunakan try-catch untuk error handling yang lebih baik
    try {
        switch ($action) {
            case 'add':
                $laporan_id = $_POST['laporan_id'] ?? '';
                $jenis_sanksi = $_POST['jenis_sanksi'] ?? '';
                $deskripsi = $_POST['deskripsi'] ?? '';
                $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
                $tanggal_selesai = $_POST['tanggal_selesai'] ?? null; // Izinkan NULL
                $diberikan_oleh = $_POST['diberikan_oleh'] ?? 'Guru Bk';

                if (empty($laporan_id) || empty($jenis_sanksi) || empty($deskripsi) || empty($tanggal_mulai)) {
                    throw new Exception("Semua field wajib diisi.");
                }

                $stmt_add = $conn->prepare("INSERT INTO sanksi (laporan_id, jenis_sanksi, deskripsi, tanggal_mulai, tanggal_selesai, diberikan_oleh) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt_add->bind_param("isssss", $laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai, $diberikan_oleh);

                if (!$stmt_add->execute()) {
                    throw new Exception("Gagal menambahkan sanksi: " . $stmt_add->error);
                }
                $stmt_add->close();

                // Update status laporan menjadi 'selesai'
                $stmt_update_laporan = $conn->prepare("UPDATE laporan SET status = 'selesai' WHERE id = ?");
                $stmt_update_laporan->bind_param("i", $laporan_id);
                if (!$stmt_update_laporan->execute()) {
                    throw new Exception("Gagal memperbarui status laporan: " . $stmt_update_laporan->error);
                }
                $stmt_update_laporan->close();

                $status = 'success';
                $msg = "Sanksi berhasil ditambahkan dan status laporan telah diperbarui.";
                break;

            case 'edit':
                $id_sanksi = $_POST['id_sanksi'] ?? '';
                $laporan_id = $_POST['laporan_id'] ?? '';
                $jenis_sanksi = $_POST['jenis_sanksi'] ?? '';
                $deskripsi = $_POST['deskripsi'] ?? '';
                $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
                $tanggal_selesai = $_POST['tanggal_selesai'] ?? null;
                $diberikan_oleh = 'Guru Bk'; // Hardcoded sesuai permintaan

                if (empty($id_sanksi) || empty($laporan_id) || empty($jenis_sanksi) || empty($deskripsi) || empty($tanggal_mulai)) {
                    throw new Exception("Semua field wajib diisi untuk edit.");
                }

                $stmt_edit = $conn->prepare("UPDATE sanksi SET laporan_id = ?, jenis_sanksi = ?, deskripsi = ?, tanggal_mulai = ?, tanggal_selesai = ?, diberikan_oleh = ? WHERE id = ?");
                $stmt_edit->bind_param("isssssi", $laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai, $diberikan_oleh, $id_sanksi);

                if (!$stmt_edit->execute()) {
                    throw new Exception("Gagal mengedit sanksi: " . $stmt_edit->error);
                }
                $stmt_edit->close();

                $status = 'success';
                $msg = "Sanksi berhasil diperbarui.";
                break;

            case 'delete':
                $id_sanksi = $_POST['id_sanksi'] ?? '';
                if (empty($id_sanksi)) {
                    throw new Exception("ID Sanksi tidak valid untuk dihapus.");
                }

                $stmt_delete = $conn->prepare("DELETE FROM sanksi WHERE id = ?");
                $stmt_delete->bind_param("i", $id_sanksi);

                if (!$stmt_delete->execute()) {
                    throw new Exception("Gagal menghapus sanksi: " . $stmt_delete->error);
                }
                $stmt_delete->close();

                $status = 'success';
                $msg = "Sanksi berhasil dihapus.";
                break;

            default:
                throw new Exception('Aksi tidak valid.');
        }
    } catch (Exception $e) {
        $status = 'error';
        $msg = $e->getMessage();
    }

    header('Location: ' . $_SERVER['PHP_SELF'] . '?status=' . $status . '&msg=' . urlencode($msg));
    exit();
}

// --- Fetch Data for Display ---
$sanksi_data = [];
$laporan_options = [];
$fetch_error = '';

if (isset($conn)) {
    // Ambil data sanksi
    $sql_sanksi = "SELECT s.id, s.laporan_id, s.jenis_sanksi, s.deskripsi, s.tanggal_mulai, s.tanggal_selesai, s.diberikan_oleh, l.kronologi AS laporan_kronologi
                   FROM sanksi s
                   LEFT JOIN laporan l ON s.laporan_id = l.id
                   ORDER BY s.id DESC";
    $result_sanksi = $conn->query($sql_sanksi);
    if ($result_sanksi) {
        $sanksi_data = $result_sanksi->fetch_all(MYSQLI_ASSOC);
    } else {
        $fetch_error = "Gagal mengambil data sanksi: " . $conn->error;
    }

    // Ambil data laporan yang masih 'diproses' untuk dropdown
    $sql_laporan = "SELECT id, kronologi FROM laporan WHERE status = 'diproses' ORDER BY id DESC";
    $result_laporan = $conn->query($sql_laporan);
    if ($result_laporan) {
        $laporan_options = $result_laporan->fetch_all(MYSQLI_ASSOC);
    } else {
        $fetch_error .= " Gagal mengambil data laporan: " . $conn->error;
    }
} else {
    $fetch_error = "Koneksi database tidak tersedia.";
}
