<?php
// sipeng/views/admin/export_pdf.php

// Ensure Composer autoloader is included for dompdf
// Adjust this path if your 'vendor' directory is not two levels up from this script.
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// IMPORTANT: Start output buffering at the very beginning to prevent any accidental output
// This ensures that only the PDF file content is sent to the browser.
ob_start();

// Include the header or directly the database connection
// Assuming header.php sets up $conn and handles authentication.
// Make sure header.php itself doesn't output anything before this script.
include 'header.php'; // Adjust path if header.php is elsewhere

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

    $sql_fetch_laporan = "
        SELECT
            l.id, l.kronologi, l.lokasi, l.tanggal_kejadian, l.bukti, l.status, l.created_at, l.updated_at,
            kl.nama_kategori,
            u.nama AS nama_pelapor,
            u.nis_nip AS nisnip_pelapor
        FROM laporan l
        LEFT JOIN kategori_laporan kl ON l.kategori_id = kl.id
        LEFT JOIN users u ON l.user_id = u.id
    ";

    if (!empty($where_clauses)) {
        $sql_fetch_laporan .= " WHERE " . implode(" AND ", $where_clauses);
    }

    $sql_fetch_laporan .= " ORDER BY l.created_at DESC";

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
    // If filter is not set, redirect back or show an error
    $_SESSION['error_message'] = "Filter tahun harus dipilih untuk mengekspor data PDF.";
    // Before redirecting, clean the buffer if output started.
    if (ob_get_length() > 0) {
        ob_end_clean();
    }
    header('Location: data_laporan.php'); // Redirect back to data_laporan page
    exit();
}

// Close connection after all operations
if (isset($conn)) {
    mysqli_close($conn);
}

// --- Generate PDF File ---
if (!empty($error_message)) {
    $_SESSION['error_message'] = "Gagal mengekspor data PDF: " . $error_message;
    // Before redirecting, clean the buffer if output started.
    if (ob_get_length() > 0) {
        ob_end_clean();
    }
    header('Location: data_laporan.php');
    exit();
}

// Create new Dompdf object
$options = new Options();
$options->set('defaultFont', 'DejaVu Sans'); // Recommended for wider character support
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // Allow loading remote images (like placeholders)
$dompdf = new Dompdf($options);

// Output buffering to capture HTML specifically for Dompdf
// This inner ob_start is specifically for the HTML content that Dompdf will render.
ob_start();
?>
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Pengaduan</title>
    <style>
    /* Define $months here if not globally available, or fetch it again */
    <?php // This is a simple re-definition for the PDF context if $months isn't global
    // Or if you only use it here. Adjust as needed.
    $months=[ '01'=>'Januari',
    '02'=>'Februari',
    '03'=>'Maret',
    '04'=>'April',
    '05'=>'Mei',
    '06'=>'Juni',
    '07'=>'Juli',
    '08'=>'Agustus',
    '09'=>'September',
    '10'=>'Oktober',
    '11'=>'November',
    '12'=>'Desember'
    ];

    ?>body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    th,
    td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    h1 {
        text-align: center;
        font-size: 18px;
        margin-bottom: 20px;
    }

    .status-terkirim {
        color: #f59e0b;
    }

    /* Tailwind yellow-500 */
    .status-diproses {
        color: #8b5cf6;
    }

    /* Tailwind purple-500 */
    .status-selesai {
        color: #10b981;
    }

    /* Tailwind green-500 */
    .status-ditolak {
        color: #ef4444;
    }

    /* Tailwind red-500 */
    </style>
</head>

<body>
    <h1>Laporan Data Pengaduan</h1>
    <p>Periode Filter:
        <?php
        $filter_info = [];
        if (!empty($filter_day)) $filter_info[] = "Tanggal: " . htmlspecialchars($filter_day);
        if (!empty($filter_month)) $filter_info[] = "Bulan: " . htmlspecialchars($months[$filter_month] ?? $filter_month);
        if (!empty($filter_year)) $filter_info[] = "Tahun: " . htmlspecialchars($filter_year);
        echo implode(", ", $filter_info);
        ?>
    </p>
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Pelapor (NIS/NIP)</th>
                <th>Kategori</th>
                <th>Kronologi</th>
                <th>Lokasi</th>
                <th>Tgl. Kejadian</th>
                <th>Status</th>
                <th>Dibuat Pada</th>
                <th>Terakhir Diubah</th>
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
                        style="font-size: 9px; color: #555;">(<?= htmlspecialchars($laporan['nisnip_pelapor'] ?? 'N/A') ?>)</span>
                </td>
                <td><?= htmlspecialchars($laporan['nama_kategori'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars(mb_strimwidth($laporan['kronologi'] ?? '', 0, 100, "...")) ?></td>
                <td><?= htmlspecialchars($laporan['lokasi'] ?? 'N/A') ?></td>
                <td><?= htmlspecialchars($laporan['tanggal_kejadian'] ? date('d M Y', strtotime($laporan['tanggal_kejadian'])) : 'N/A') ?>
                </td>
                <td>
                    <span class="status-<?= htmlspecialchars($laporan['status']) ?>">
                        <?= htmlspecialchars(ucfirst($laporan['status'] ?? 'N/A')) ?>
                    </span>
                </td>
                <td><?= htmlspecialchars($laporan['created_at'] ? date('d M Y H:i', strtotime($laporan['created_at'])) : 'N/A') ?>
                </td>
                <td><?= htmlspecialchars($laporan['updated_at'] ? date('d M Y H:i', strtotime($laporan['updated_at'])) : 'N/A') ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php else: ?>
            <tr>
                <td colspan="9" style="text-align: center;">Tidak ada data laporan ditemukan dengan filter saat ini.
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
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
// This is critical to ensure only the PDF binary is sent.
if (ob_get_length() > 0) {
    ob_end_clean();
}

// Output the generated PDF to Browser
$filename = 'laporan_pengaduan_' . date('Ymd_His') . '.pdf';
$dompdf->stream($filename, ["Attachment" => true]); // true = download, false = open in browser
exit();
?>