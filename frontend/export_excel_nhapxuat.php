<?php
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include('../backend/db_connect.php');

$mahang = $_GET['mahang'] ?? '';
$ngaybatdau = $_GET['ngaybatdau'] ?? '';
$ngayketthuc = $_GET['ngayketthuc'] ?? '';

$conds = [];
if ($mahang !== '') {
    $mahang_safe = $conn->real_escape_string($mahang);
    $conds[] = "p.mahang = '$mahang_safe'";
}
if ($ngaybatdau !== '') {
    $nbd_safe = $conn->real_escape_string($ngaybatdau);
    $conds[] = "p.ngaytao >= '$nbd_safe 00:00:00'";
}
if ($ngayketthuc !== '') {
    $nkt_safe = $conn->real_escape_string($ngayketthuc);
    $conds[] = "p.ngaytao <= '$nkt_safe 23:59:59'";
}
$where = count($conds) ? "WHERE " . implode(" AND ", $conds) : "";

$sql = "
    SELECT p.*, h.tenhang
    FROM phieunhapxuat p
    LEFT JOIN hanghoa h ON p.mahang = h.mahang
    $where
    ORDER BY p.ngaytao DESC
";

$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Nhập Xuất Kho');

$headers = ['Mã hàng', 'Tên hàng', 'Số lượng', 'Loại', 'Người thực hiện', 'Ghi chú', 'Ngày tạo'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['mahang']);
    $sheet->setCellValue('B' . $rowNumber, $row['tenhang']);
    $sheet->setCellValue('C' . $rowNumber, $row['soluong']);
    $sheet->setCellValue('D' . $rowNumber, ucfirst($row['loai']));
    $sheet->setCellValue('E' . $rowNumber, $row['nguoi_thuc_hien']);
    $sheet->setCellValue('F' . $rowNumber, $row['ghichu']);
    $sheet->setCellValue('G' . $rowNumber, date('d/m/Y H:i', strtotime($row['ngaytao'])));
    $rowNumber++;
}

foreach (range('A', 'G') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Nhap_Xuat_Kho.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
