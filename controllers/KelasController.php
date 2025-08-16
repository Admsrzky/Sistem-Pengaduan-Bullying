<?php

include '../../config/database.php';

/**
 * =================================================================
 * KelasController.php
 * (Menggunakan Notifikasi via URL & Pola PRG)
 * =================================================================
 */

// session_start() tidak lagi diperlukan untuk metode notifikasi ini.

// Diasumsikan koneksi '$conn' sudah tersedia dari file yang meng-include controller ini.

// =================================================================
// BAGIAN PEMROSESAN FORM (CREATE, UPDATE, DELETE)
// =================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($conn)) {
        $errorMsg = urlencode("Koneksi database tidak ditemukan.");
        header('Location: ' . $_SERVER['PHP_SELF'] . '?status=error&msg=' . $errorMsg);
        exit();
    }

    $action = $_POST['action'] ?? '';
    $redirectUrl = $_SERVER['PHP_SELF']; // Halaman tujuan redirect

    switch ($action) {
        case 'add':
            $nama_kelas = trim($_POST['nama_kelas'] ?? '');
            if (!empty($nama_kelas)) {
                $safe_nama = mysqli_real_escape_string($conn, $nama_kelas);
                $sql = "INSERT INTO kelas (nama_kelas) VALUES ('$safe_nama')";
                if (mysqli_query($conn, $sql)) {
                    $status = 'success';
                    $msg = 'Kelas baru berhasil ditambahkan!';
                } else {
                    $status = 'error';
                    $msg = 'Gagal menambahkan kelas: ' . mysqli_error($conn);
                }
            } else {
                $status = 'error';
                $msg = 'Nama Kelas tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id = trim($_POST['id_kelas'] ?? '');
            $nama_kelas = trim($_POST['nama_kelas'] ?? '');
            if (!empty($id) && !empty($nama_kelas)) {
                $safe_id = mysqli_real_escape_string($conn, $id);
                $safe_nama = mysqli_real_escape_string($conn, $nama_kelas);
                $sql = "UPDATE kelas SET nama_kelas = '$safe_nama' WHERE id = '$safe_id'";
                if (mysqli_query($conn, $sql)) {
                    $status = 'success';
                    $msg = 'Data Kelas berhasil diubah!';
                } else {
                    $status = 'error';
                    $msg = 'Gagal mengubah data: ' . mysqli_error($conn);
                }
            } else {
                $status = 'error';
                $msg = 'Data tidak lengkap untuk proses edit.';
            }
            break;

        case 'delete':
            $id = trim($_POST['id_kelas'] ?? '');
            if (!empty($id)) {
                $safe_id = mysqli_real_escape_string($conn, $id);
                $sql = "DELETE FROM kelas WHERE id = '$safe_id'";
                if (mysqli_query($conn, $sql)) {
                    $status = 'success';
                    $msg = 'Kelas berhasil dihapus!';
                } else {
                    $status = 'error';
                    $msg = 'Gagal menghapus kelas.';
                }
            } else {
                $status = 'error';
                $msg = 'ID Kelas tidak valid.';
            }
            break;

        default:
            $status = 'error';
            $msg = 'Aksi yang diminta tidak valid.';
            break;
    }

    // Redirect kembali ke halaman yang sama dengan membawa status dan pesan di URL
    header('Location: ' . $redirectUrl . '?status=' . $status . '&msg=' . urlencode($msg));
    exit();
}

// =================================================================
// BAGIAN PENGAMBILAN DATA (READ)
// =================================================================
$kelas_data = [];
$fetch_error = '';

if (isset($conn)) {
    $sql_fetch = "SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas ASC";
    $result = mysqli_query($conn, $sql_fetch);
    if ($result) {
        $kelas_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $fetch_error = 'Gagal mengambil data kelas: ' . mysqli_error($conn);
    }
} else {
    $fetch_error = "Koneksi database tidak tersedia untuk mengambil data.";
}

// Koneksi sebaiknya ditutup di file footer.php setelah semua konten selesai dimuat.