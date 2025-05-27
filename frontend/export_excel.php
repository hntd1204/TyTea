<?php
require '../vendor/autoload.php'; // Đường dẫn tới autoload của composer

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include('../backend/db_connect.php');

// Lấy params lọc giống như hanghoa.php
$search = $_GET['search'] ?? '';
$filter_nhom = $_GET['filter_nhom'] ?? '';
$filter_loai = $_GET['filter_loai'] ?? '';

$cond = [];
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $cond[] = "(mahang LIKE '%$s%' OR tenhang LIKE '%$s%' OR nhacungcap LIKE '%$s%')";
}
if (!empty($filter_nhom)) {
    $f = $conn->real_escape_string($filter_nhom);
    $cond[] = "nhomhang = '$f'";
}
if (!empty($filter_loai)) {
    $l = $conn->real_escape_string($filter_loai);
    $cond[] = "loaihang = '$l'";
}
$where = count($cond) ? "WHERE " . implode(" AND ", $cond) : "";

// Lấy toàn bộ dữ liệu phù hợp
$sql = "SELECT * FROM hanghoa $where ORDER BY id DESC";
$result = $conn->query($sql);

// Tạo file Excel
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Danh sách hàng hóa');

// Tiêu đề cột
$headers = ['Mã hàng', 'Tên hàng', 'Giá nhập', 'Loại', 'Nhóm', 'Số lượng', 'Đơn vị', 'Tồn kho', 'Nhà cung cấp', 'Ghi chú'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

// Dữ liệu hàng hóa bắt đầu từ dòng 2
$rowNumber = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->setCellValue('A' . $rowNumber, $row['mahang']);
    $sheet->setCellValue('B' . $rowNumber, $row['tenhang']);
    $sheet->setCellValue('C' . $rowNumber, $row['giavon']);
    $sheet->setCellValue('D' . $rowNumber, $row['loaihang']);
    $sheet->setCellValue('E' . $rowNumber, $row['nhomhang']);
    $sheet->setCellValue('F' . $rowNumber, $row['soluong']);
    $sheet->setCellValue('G' . $rowNumber, $row['donvitinh']);
    $sheet->setCellValue('H' . $rowNumber, $row['tonkho']);
    $sheet->setCellValue('I' . $rowNumber, $row['nhacungcap']);
    $sheet->setCellValue('J' . $rowNumber, $row['ghichu']);
    $rowNumber++;
}

// Tự động điều chỉnh độ rộng cột
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Xuất file Excel
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Danh_sach_hang_hoa.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
