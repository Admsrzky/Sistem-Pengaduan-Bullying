<?php

include '../../config/database.php';


/**
 * =================================================================
 * JabatanController.php
 * (Menggunakan Notifikasi via URL & Pola PRG)
 * =================================================================
 */

// session_start() tidak diperlukan untuk metode notifikasi ini.

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
            $nama_jabatan = trim($_POST['nama_jabatan'] ?? '');
            if (!empty($nama_jabatan)) {
                $safe_nama = mysqli_real_escape_string($conn, $nama_jabatan);

                // Cek duplikasi nama jabatan
                $check_sql = "SELECT id FROM jabatan_guru WHERE nama_jabatan = '$safe_nama'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $status = 'error';
                    $msg = 'Jabatan "' . htmlspecialchars($nama_jabatan) . '" sudah ada.';
                } else {
                    // Lanjutkan insert jika tidak ada duplikasi
                    $sql = "INSERT INTO jabatan_guru (nama_jabatan) VALUES ('$safe_nama')";
                    if (mysqli_query($conn, $sql)) {
                        $status = 'success';
                        $msg = 'Jabatan baru berhasil ditambahkan!';
                    } else {
                        $status = 'error';
                        $msg = 'Gagal menambahkan jabatan: ' . mysqli_error($conn);
                    }
                }
            } else {
                $status = 'error';
                $msg = 'Nama Jabatan tidak boleh kosong.';
            }
            break;

        case 'edit':
            $id = trim($_POST['id_jabatan'] ?? '');
            $nama_jabatan = trim($_POST['nama_jabatan'] ?? '');
            if (!empty($id) && !empty($nama_jabatan)) {
                $safe_id = mysqli_real_escape_string($conn, $id);
                $safe_nama = mysqli_real_escape_string($conn, $nama_jabatan);

                // Cek duplikasi (kecuali untuk dirinya sendiri)
                $check_sql = "SELECT id FROM jabatan_guru WHERE nama_jabatan = '$safe_nama' AND id != '$safe_id'";
                $check_result = mysqli_query($conn, $check_sql);
                if (mysqli_num_rows($check_result) > 0) {
                    $status = 'error';
                    $msg = 'Nama jabatan "' . htmlspecialchars($nama_jabatan) . '" sudah digunakan.';
                } else {
                    $sql = "UPDATE jabatan_guru SET nama_jabatan = '$safe_nama' WHERE id = '$safe_id'";
                    if (mysqli_query($conn, $sql)) {
                        $status = 'success';
                        $msg = 'Data Jabatan berhasil diubah!';
                    } else {
                        $status = 'error';
                        $msg = 'Gagal mengubah data: ' . mysqli_error($conn);
                    }
                }
            } else {
                $status = 'error';
                $msg = 'Data tidak lengkap untuk proses edit.';
            }
            break;

        case 'delete':
            $id = trim($_POST['id_jabatan'] ?? '');
            if (!empty($id)) {
                $safe_id = mysqli_real_escape_string($conn, $id);

                // Cek apakah jabatan masih digunakan oleh guru
                $check_guru_sql = "SELECT COUNT(*) AS total_guru FROM guru WHERE jabatan_id = '$safe_id'";
                $check_guru_result = mysqli_query($conn, $check_guru_sql);
                $guru_count = mysqli_fetch_assoc($check_guru_result)['total_guru'];

                if ($guru_count > 0) {
                    $status = 'error';
                    $msg = 'Jabatan tidak dapat dihapus karena masih digunakan oleh ' . $guru_count . ' guru.';
                } else {
                    $sql = "DELETE FROM jabatan_guru WHERE id = '$safe_id'";
                    if (mysqli_query($conn, $sql)) {
                        $status = 'success';
                        $msg = 'Jabatan berhasil dihapus!';
                    } else {
                        $status = 'error';
                        $msg = 'Gagal menghapus jabatan.';
                    }
                }
            } else {
                $status = 'error';
                $msg = 'ID Jabatan tidak valid.';
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
$jabatan_data = [];
$fetch_error = '';

if (isset($conn)) {
    $sql_fetch = "SELECT id, nama_jabatan FROM jabatan_guru ORDER BY nama_jabatan ASC";
    $result = mysqli_query($conn, $sql_fetch);
    if ($result) {
        $jabatan_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        $fetch_error = 'Gagal mengambil data jabatan: ' . mysqli_error($conn);
    }
} else {
    $fetch_error = "Koneksi database tidak tersedia untuk mengambil data.";
}

// Jangan tutup koneksi di sini, biarkan file footer yang menanganinya