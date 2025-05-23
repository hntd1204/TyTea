<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

$hoadon_id = $_GET['id'] ?? 0;
if ($hoadon_id == 0) {
    header("Location: hoadon.php");
    exit;
}

// Lấy thông tin hóa đơn
$hoadon = $conn->query("SELECT * FROM hoadon WHERE id = $hoadon_id")->fetch_assoc();

// Lấy chi tiết hóa đơn
$sql = "SELECT ct.*, hh.tenhang, hh.mahang 
        FROM chitiethoadon ct 
        JOIN hanghoa hh ON ct.hanghoa_id = hh.id 
        WHERE ct.hoadon_id = $hoadon_id";
$items = $conn->query($sql);
?>

<div id="page-content-wrapper" class="p-4">
    <h3 class="mb-3">Chi tiết hóa đơn #<?= $hoadon_id ?></h3>
    <p><strong>Ngày lập:</strong> <?= $hoadon['ngaylap'] ?></p>
    <p><strong>Tổng tiền:</strong> <?= number_format($hoadon['tongtien'], 0, ',', '.') ?>đ</p>

    <table class="table table-bordered mt-4">
        <thead class="thead-light">
            <tr>
                <th>Mã hàng</th>
                <th>Tên hàng</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $items->fetch_assoc()): ?>
            <tr>
                <td><?= $row['mahang'] ?></td>
                <td><?= $row['tenhang'] ?></td>
                <td><?= $row['soluong'] ?></td>
                <td><?= number_format($row['thanhtien'], 0, ',', '.') ?>đ</td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="../backend/delete_hoadon.php?id=<?= $hoadon_id ?>" class="btn btn-danger"
        onclick="return confirm('Bạn có chắc chắn muốn xóa hóa đơn này không?');">
        <i class="fas fa-trash-alt"></i> Xóa hóa đơn
    </a>



    <a href="hoadon.php" class="btn btn-secondary">← Quay lại</a>
</div>

<?php include('layout/footer.php'); ?>