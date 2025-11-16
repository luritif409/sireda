<?php
/**
 * Script to generate the ImportUserTemplate.xlsx file
 * Run this from the project root:
 *   php artisan tinker
 *   include 'generate_import_template.php'
 *
 * Or save this file in the app and run it as part of a command/seeder.
 */

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\PatternFill;
use PhpOffice\PhpSpreadsheet\Alignment;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Import Users');

// Set header row (row 1)
$headers = [
    'nama_lengkap',
    'email',
    'password',
    'role',
    'nim',
    'judul_tugas_akhir',
    'dosen_pembimbing',
];

foreach ($headers as $col => $header) {
    $columnLetter = chr(65 + $col); // A, B, C, ...
    $sheet->setCellValue($columnLetter . '1', $header);
}

// Style header row (bold, light gray background)
$headerStyle = [
    'font' => ['bold' => true, 'color' => ['rgb' => '000000']],
    'fill' => ['fillType' => PatternFill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E0E0']],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
];

$sheet->getStyle('A1:G1')->applyFromArray($headerStyle);

// Add example row 1: Mahasiswa
$sheet->setCellValue('A2', 'Ahmad Fauzi');
$sheet->setCellValue('B2', 'ahmad.fauzi@example.com');
$sheet->setCellValue('C2', 'password123');
$sheet->setCellValue('D2', 'mahasiswa');
$sheet->setCellValue('E2', '2022001');
$sheet->setCellValue('F2', 'Sistem Informasi Manajemen');
$sheet->setCellValue('G2', 'Dr. Budi Santoso');

// Add example row 2: Dosen
$sheet->setCellValue('A3', 'Dr. Budi Santoso');
$sheet->setCellValue('B3', 'budi.santoso@example.com');
$sheet->setCellValue('C3', 'password123');
$sheet->setCellValue('D3', 'dosen');
// E3, F3, G3 left empty (optional for dosen)

// Set column widths
$sheet->getColumnDimension('A')->setWidth(20);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(15);
$sheet->getColumnDimension('D')->setWidth(12);
$sheet->getColumnDimension('E')->setWidth(12);
$sheet->getColumnDimension('F')->setWidth(25);
$sheet->getColumnDimension('G')->setWidth(20);

// Freeze header row
$sheet->freezePane('A2');

// Save to public/templates directory
$filePath = public_path('templates/ImportUserTemplate.xlsx');
$writer = new Xlsx($spreadsheet);
$writer->save($filePath);

echo "Template generated successfully at: " . $filePath;
