<?php
// sipeng/views/admin/export_pdf.php

// Ensure Composer autoloader is included for dompdf
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// IMPORTANT: Start output buffering at the very beginning to prevent any accidental output
ob_start();

// Include the database connection directly or a minimal header if necessary.
include '../../config/database.php'; // Adjust path if config/database.php is elsewhere

// Set default timezone for consistent date handling
date_default_timezone_set('Asia/Jakarta');

// Initialize variables
$laporan_data = [];
$error_message = '';

// --- Handle Filter Parameters (same as data_laporan.php) ---
$filter_day = filter_input(INPUT_GET, 'filter_day', FILTER_SANITIZE_NUMBER_INT);
$filter_month = filter_input(INPUT_GET, 'filter_month', FILTER_SANITIZE_NUMBER_INT);
$filter_year = filter_input(INPUT_GET, 'filter_year', FILTER_SANITIZE_NUMBER_INT);

// Flag to check if any filter is set (at least year is mandatory)
$is_filter_set = !empty($filter_year);

// Ensure database connection is available
if (!isset($conn) || $conn->connect_error) {
    $error_message = "Koneksi database gagal: " . ($conn->connect_error ?? 'Unknown error');
}

// Only fetch data if filter is set and no connection errors
if ($is_filter_set && empty($error_message) && isset($conn) && $conn->ping()) {
    $where_clauses = [];
    $params = [];
    $param_types = '';

    // Existing filters
    if (!empty($filter_day)) {
        $where_clauses[] = "DAY(l.tanggal_kejadian) = ?";
        $params[] = $filter_day;
        $param_types .= 'i';
    }
    if (!empty($filter_month)) {
        $where_clauses[] = "MONTH(l.tanggal_kejadian) = ?";
        $params[] = $filter_month;
        $param_types .= 'i';
    }
    if (!empty($filter_year)) {
        $where_clauses[] = "YEAR(l.tanggal_kejadian) = ?";
        $params[] = $filter_year;
        $param_types .= 'i';
    }

    // Filter for status = 'selesai'
    $where_clauses[] = "l.status = ?";
    $params[] = 'selesai';
    $param_types .= 's'; // 's' for string status

    $sql_fetch_laporan = "
        SELECT
            l.id, l.kronologi, l.lokasi, l.tanggal_kejadian, l.bukti, l.status, l.created_at, l.updated_at,
            kl.nama_kategori,
            u.nama AS nama_pelapor,
            u.nis_nip AS nisnip_pelapor,
            s.jenis_sanksi,
            s.deskripsi AS deskripsi_sanksi,
            s.tanggal_mulai AS sanksi_tanggal_mulai,
            s.tanggal_selesai AS sanksi_tanggal_selesai,
            s.diberikan_oleh AS sanksi_diberikan_oleh
        FROM laporan l
        LEFT JOIN kategori_laporan kl ON l.kategori_id = kl.id
        LEFT JOIN users u ON l.user_id = u.id
        LEFT JOIN sanksi s ON l.id = s.laporan_id
    ";

    if (!empty($where_clauses)) {
        $sql_fetch_laporan .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $sql_fetch_laporan .= " ORDER BY l.id DESC, s.id ASC";

    $stmt_fetch_laporan = mysqli_prepare($conn, $sql_fetch_laporan);

    if ($stmt_fetch_laporan) {
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt_fetch_laporan, $param_types, ...$params);
        }
        mysqli_stmt_execute($stmt_fetch_laporan);
        $result_fetch_laporan = mysqli_stmt_get_result($stmt_fetch_laporan);

        if ($result_fetch_laporan) {
            while ($row = mysqli_fetch_assoc($result_fetch_laporan)) {
                $laporan_data[] = $row;
            }
            mysqli_free_result($result_fetch_laporan);
        } else {
            $error_message = 'Gagal mengambil data laporan: ' . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt_fetch_laporan);
    } else {
        $error_message = 'Gagal menyiapkan statement fetch laporan: ' . mysqli_error($conn);
    }
} else {
    $_SESSION['error_message'] = "Filter tahun harus dipilih untuk mengekspor data PDF.";
    if (ob_get_length() > 0) {
        ob_end_clean();
    }
    header('Location: data_laporan.php');
    exit();
}

// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}

// --- Generate PDF File ---
if (!empty($error_message)) {
    $_SESSION['error_message'] = "Gagal mengekspor data PDF: " . $error_message;
    if (ob_get_length() > 0) {
        ob_end_clean();
    }
    header('Location: data_laporan.php');
    exit();
}

// Create new Dompdf object
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans');
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Define $months here for the PDF context
$months = [
    '01' => 'Januari',
    '02' => 'Februari',
    '03' => 'Maret',
    '04' => 'April',
    '05' => 'Mei',
    '06' => 'Juni',
    '07' => 'Juli',
    '08' => 'Agustus',
    '09' => 'September',
    '10' => 'Oktober',
    '11' => 'November',
    '12' => 'Desember'
];

ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pengaduan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 8.5px;
            color: #333;
            line-height: 1.3;
        }

        h1 {
            text-align: center;
            font-size: 16px;
            margin-bottom: 5px;
            color: #2c3e50;
        }

        h2 {
            text-align: center;
            font-size: 12px;
            margin-bottom: 15px;
            color: #555;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #e0e0e0;
            padding: 5px 3px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background-color: #f8f8f8;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
        }

        tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        .status-terkirim {
            color: #f59e0b;
        }

        .status-diproses {
            color: #8b5cf6;
        }

        .status-selesai {
            color: #10b981;
        }

        .status-ditolak {
            color: #ef4444;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            position: fixed;
            bottom: 0px;
            left: 0px;
            right: 0px;
            height: 30px;
            font-size: 8px;
            color: #777;
            text-align: right;
            padding-right: 10px;
        }

        /* NEW: Styles for signatures */
        .signatures {
            width: 100%;
            margin-top: 30px;
            display: table;
            /* Use table display for columns */
            table-layout: fixed;
            /* Ensures columns are equal width */
        }

        .signature-column {
            width: 50%;
            /* Two columns */
            display: table-cell;
            text-align: center;
            vertical-align: top;
            padding: 0 10px;
        }

        .signature-placeholder {
            margin-top: 50px;
            /* Space for signature */
            /* border-bottom: 1px solid #000; */
            display: inline-block;
            width: 80%;
            height: 1px;
            /* Line for signature */
        }

        .signature-name {
            margin-top: 5px;
            font-weight: bold;
        }

        .signature-nip {
            font-weight: bold;
            display: flex;
            align-items: start;
            justify-content: start;
        }

        .signature-title {
            font-size: 8px;
            color: #555;
            margin-top: 2px;
        }
    </style>
</head>

<body>
    <h1>Rekapitulasi Data Laporan Pengaduan</h1>
    <br>

    <table>
        <thead>
            <tr>
                <th style="width: 3%;">No.</th>
                <th style="width: 10%;">Pelapor (NIS/NIP)</th>
                <th style="width: 8%;">Kategori</th>
                <th style="width: 15%;">Kronologi</th>
                <th style="width: 8%;">Lokasi</th>
                <th style="width: 8%;">Tgl. Kejadian</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 8%;">Jenis Sanksi</th>
                <th style="width: 15%;">Deskripsi Sanksi</th>
                <th style="width: 8%;">Dibuat Pada</th>
                <th style="width: 8%;">Terakhir Diubah</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($laporan_data)): ?>
                <?php $no = 1; ?>
                <?php foreach ($laporan_data as $laporan): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td>
                            <?= htmlspecialchars($laporan['nama_pelapor'] ?? 'N/A') ?><br>
                            <span
                                style="font-size: 7.5px; color: #555;">(<?= htmlspecialchars($laporan['nisnip_pelapor'] ?? 'N/A') ?>)</span>
                        </td>
                        <td><?= htmlspecialchars($laporan['nama_kategori'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars(mb_strimwidth($laporan['kronologi'] ?? '', 0, 80, "...")) ?></td>
                        <td><?= htmlspecialchars($laporan['lokasi'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($laporan['tanggal_kejadian'] ? date('d M Y', strtotime($laporan['tanggal_kejadian'])) : 'N/A') ?>
                        </td>
                        <td>
                            <span class="status-<?= htmlspecialchars($laporan['status']) ?>">
                                <?= htmlspecialchars(ucfirst($laporan['status'] ?? 'N/A')) ?>
                            </span>
                        </td>
                        <td>
                            <?= htmlspecialchars($laporan['jenis_sanksi'] ?? 'N/A') ?>
                        </td>
                        <td>
                            <?= htmlspecialchars(mb_strimwidth($laporan['deskripsi_sanksi'] ?? 'N/A', 0, 80, "...")) ?>
                        </td>
                        <td><?= htmlspecialchars($laporan['created_at'] ? date('d M Y H:i', strtotime($laporan['created_at'])) : 'N/A') ?>
                        </td>
                        <td><?= htmlspecialchars($laporan['updated_at'] ? date('d M Y H:i', strtotime($laporan['updated_at'])) : 'N/A') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="11" class="text-center">Tidak ada data laporan ditemukan dengan filter saat ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="signatures">
        <div class="signature-column">
            <p>Mengetahui,</p>
            <p class="signature-title">Kepala Guru BK</p>
            <div class="signature-placeholder"></div>
            <p class="signature-name">(ucu)</p>
            <p class="signature-nip">NIP : 202112019 </p>
        </div>
        <div class="signature-column">
            <p>Cilegon, <?= date('d') . ' ' . ($months[date('m')] ?? date('m')) . ' ' . date('Y') ?></p>
            <p class="signature-title">Kepala Sekolah</p>
            <div class="signature-placeholder"></div>
            <p class="signature-name">( Dra. Hj. Maryati, M.Pd)</p>
            <p class="signature-nip">NIP : </p>
        </div>
    </div>

    <div class="footer">
        Dicetak pada: <?= date('d M Y H:i:s') ?>
    </div>
</body>

</html>
<?php
$html = ob_get_clean(); // Get the HTML content buffered for Dompdf

$dompdf->loadHtml($html);

// (Optional) Set paper size and orientation
$dompdf->setPaper('A4', 'landscape'); // 'portrait' or 'landscape'

// Render the HTML as PDF
$dompdf->render();

// Clear any global output buffer that might have been started before (e.g., from header.php)
if (ob_get_length() > 0) {
    ob_end_clean();
}

// Output the generated PDF to Browser
$filename = 'laporan_pengaduan_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ["Attachment" => true]); // true = download, false = open in browser
exit();
?>