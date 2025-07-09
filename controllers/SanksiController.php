<?php
// controllers/SanksiController.php

// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan koneksi database tersedia
if (!isset($conn)) {
    include_once '../../config/database.php';
}

class SanksiController
{
    private $conn;

    public function __construct($db_connection)
    {
        $this->conn = $db_connection;
    }

    // Ambil semua data sanksi beserta kronologi laporan terkait
    public function getAllSanksi()
    {
        // Lakukan JOIN dengan tabel 'laporan' untuk mengambil kronologi
        $sql = "SELECT s.id, s.laporan_id, s.jenis_sanksi, s.deskripsi, s.tanggal_mulai, s.tanggal_selesai, s.diberikan_oleh, l.kronologi AS laporan_kronologi
                FROM sanksi s
                LEFT JOIN laporan l ON s.laporan_id = l.id
                ORDER BY s.id DESC"; // Order by ID sanksi
        $result = $this->conn->query($sql);
        $sanksi = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $sanksi[] = $row;
            }
        }
        return $sanksi;
    }

    // Ambil semua data laporan untuk dropdown (menggunakan 'kronologi')
    // HANYA laporan dengan status 'diproses'
    public function getAllLaporan()
    {
        // Menggunakan 'kronologi' sebagai deskripsi laporan karena tidak ada kolom 'judul'
        // Menambahkan WHERE status = 'diproses'
        $sql = "SELECT id, kronologi FROM laporan WHERE status = 'diproses' ORDER BY id DESC";
        $result = $this->conn->query($sql);
        $laporan = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $laporan[] = $row;
            }
        }
        return $laporan;
    }

    // Tambah sanksi baru
    public function addSanksi($laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai, $diberikan_oleh)
    {
        $tanggal_selesai_param = !empty($tanggal_selesai) ? $tanggal_selesai : NULL;

        $stmt = $this->conn->prepare("INSERT INTO sanksi (laporan_id, jenis_sanksi, deskripsi, tanggal_mulai, tanggal_selesai, diberikan_oleh) VALUES (?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            // Tipe binding: 'isssss' -> integer, string, string, string, string, string
            $stmt->bind_param("isssss", $laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai_param, $diberikan_oleh);
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                $_SESSION['error_message'] = "Error saat menambahkan sanksi: " . $stmt->error;
                $stmt->close();
                return false;
            }
        } else {
            $_SESSION['error_message'] = "Error saat menyiapkan query: " . $this->conn->error;
            return false;
        }
    }

    // Edit sanksi
    public function editSanksi($id, $laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai, $diberikan_oleh_value)
    {
        $tanggal_selesai_param = !empty($tanggal_selesai) ? $tanggal_selesai : NULL;

        $stmt = $this->conn->prepare("UPDATE sanksi SET laporan_id = ?, jenis_sanksi = ?, deskripsi = ?, tanggal_mulai = ?, tanggal_selesai = ?, diberikan_oleh = ? WHERE id = ?");
        if ($stmt) {
            // Tipe binding 's' untuk diberikan_oleh_value (string)
            $stmt->bind_param("isssssi", $laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai_param, $diberikan_oleh_value, $id);
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                $_SESSION['error_message'] = "Error saat mengedit sanksi: " . $stmt->error;
                $stmt->close();
                return false;
            }
        } else {
            $_SESSION['error_message'] = "Error saat menyiapkan query: " . $this->conn->error;
            return false;
        }
    }

    // Hapus sanksi
    public function deleteSanksi($id)
    {
        $stmt = $this->conn->prepare("DELETE FROM sanksi WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $stmt->close();
                return true;
            } else {
                $_SESSION['error_message'] = "Error saat menghapus sanksi: " . $stmt->error;
                $stmt->close();
                return false;
            }
        } else {
            $_SESSION['error_message'] = "Error saat menyiapkan query: " . $this->conn->error;
            return false;
        }
    }
}

// Inisialisasi controller
$sanksiController = new SanksiController($conn);

// Tangani aksi POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'add':
            $laporan_id = $_POST['laporan_id'] ?? '';
            $jenis_sanksi = $_POST['jenis_sanksi'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
            $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
            $diberikan_oleh = $_POST['diberikan_oleh'] ?? 'Guru Bk'; // Default 'Guru Bk' jika kosong

            if ($sanksiController->addSanksi($laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai, $diberikan_oleh)) {
                // Setelah sanksi ditambahkan, update status laporan menjadi 'selesai'
                if ($laporan_id) {
                    $stmt_update_laporan_status = $conn->prepare("UPDATE laporan SET status = 'selesai' WHERE id = ?");
                    if ($stmt_update_laporan_status) {
                        $stmt_update_laporan_status->bind_param("i", $laporan_id);
                        $stmt_update_laporan_status->execute();
                        $stmt_update_laporan_status->close();
                    }
                }
                $_SESSION['success_message'] = "Sanksi untuk Laporan ID $laporan_id berhasil ditambahkan dan status laporan diperbarui menjadi selesai.";
            }
            break;
        case 'edit':
            $id_sanksi = $_POST['id_sanksi'] ?? '';
            $laporan_id = $_POST['laporan_id'] ?? '';
            $jenis_sanksi = $_POST['jenis_sanksi'] ?? '';
            $deskripsi = $_POST['deskripsi'] ?? '';
            $tanggal_mulai = $_POST['tanggal_mulai'] ?? '';
            $tanggal_selesai = $_POST['tanggal_selesai'] ?? '';
            $diberikan_oleh_fixed = 'Guru Bk'; // Saat EDIT, hardcode nilai diberikan_oleh menjadi "Guru Bk"

            if ($sanksiController->editSanksi($id_sanksi, $laporan_id, $jenis_sanksi, $deskripsi, $tanggal_mulai, $tanggal_selesai, $diberikan_oleh_fixed)) {
                $_SESSION['success_message'] = "Sanksi ID $id_sanksi berhasil diperbarui.";
            }
            break;
        case 'delete':
            $id_sanksi = $_POST['id_sanksi'] ?? '';
            if ($sanksiController->deleteSanksi($id_sanksi)) {
                $_SESSION['success_message'] = "Sanksi berhasil dihapus.";
            }
            break;
    }
    header("Location: Data_Sanksi.php");
    exit();
}
