<?php
// sipeng/views/admin/export_excel.php

// Ensure Composer autoloader is included for PhpSpreadsheet
// Adjust this path if your 'vendor' directory is not two levels up from this script.
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// IMPORTANT: Start output buffering at the very beginning to prevent any accidental output
// This ensures that only the Excel file content is sent to the browser.
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
    $_SESSION['error_message'] = "Filter tahun harus dipilih untuk mengekspor data Excel.";
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

// --- Generate Excel File ---
if (!empty($error_message)) {
    $_SESSION['error_message'] = "Gagal mengekspor data Excel: " . $error_message;
    // Before redirecting, clean the buffer if output started.
    if (ob_get_length() > 0) {
        ob_end_clean();
    }
    header('Location: data_laporan.php');
    exit();
}

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()->setCreator("SIPENG Admin")
    ->setLastModifiedBy("SIPENG Admin")
    ->setTitle("Laporan Pengaduan")
    ->setSubject("Data Pengaduan")
    ->setDescription("Export data pengaduan dari sistem SIPENG.")
    ->setKeywords("pengaduan laporan excel")
    ->setCategory("Laporan");

// Add headers
$headers = [
    'No.',
    'Pelapor (NIS/NIP)',
    'Kategori',
    'Kronologi',
    'Lokasi',
    'Tanggal Kejadian',
    'Status',
    'Dibuat Pada',
    'Terakhir Diubah'
];
$sheet->fromArray($headers, NULL, 'A1');

// Style headers
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => 'FFFFFFFF'], // White text
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF4299E1'], // A blue color
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF000000'],
        ],
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
    ],
];
$sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray($headerStyle);
$sheet->getRowDimension(1)->setRowHeight(25); // Set header row height

// Add data rows
$rowNumber = 2;
foreach ($laporan_data as $index => $laporan) {
    $sheet->setCellValue('A' . $rowNumber, $index + 1);
    $sheet->setCellValue('B' . $rowNumber, ($laporan['nama_pelapor'] ?? 'N/A') . ' (' . ($laporan['nisnip_pelapor'] ?? 'N/A') . ')');
    $sheet->setCellValue('C' . $rowNumber, $laporan['nama_kategori'] ?? 'N/A');
    $sheet->setCellValue('D' . $rowNumber, $laporan['kronologi'] ?? '');
    $sheet->setCellValue('E' . $rowNumber, $laporan['lokasi'] ?? 'N/A');
    $sheet->setCellValue('F' . $rowNumber, $laporan['tanggal_kejadian'] ? date('d M Y', strtotime($laporan['tanggal_kejadian'])) : 'N/A');
    $sheet->setCellValue('G' . $rowNumber, ucfirst($laporan['status'] ?? 'N/A'));
    $sheet->setCellValue('H' . $rowNumber, $laporan['created_at'] ? date('d M Y H:i', strtotime($laporan['created_at'])) : 'N/A');
    $sheet->setCellValue('I' . $rowNumber, $laporan['updated_at'] ? date('d M Y H:i', strtotime($laporan['updated_at'])) : 'N/A');
    $rowNumber++;
}

// Auto-size columns for all data
foreach (range('A', $sheet->getHighestColumn()) as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Generate filename based on current date and time
$filename = 'laporan_pengaduan_' . date('Ymd_His', time()) . '.xlsx';

// Clear any previous output before sending headers
if (ob_get_length() > 0) {
    ob_end_clean();
}

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
// If you're serving over HTTPS, you might need this
// header('Cache-Control: private',false); // no-cache, no-store, must-revalidate
// header('Pragma: public');

// Create a writer and save the spreadsheet
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();