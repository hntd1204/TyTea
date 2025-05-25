<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$ncc = $_GET['ncc'] ?? '';
if (!$ncc) {
    header("Location: nhacungcap.php");
    exit;
}

$ncc_sql = $conn->real_escape_string($ncc);
$products = $conn->query("SELECT * FROM hanghoa WHERE nhacungcap = '$ncc_sql'");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Sản phẩm từ nhà cung cấp: <strong><?= htmlspecialchars($ncc ?? '') ?></strong></h2>
    <a href="nhacungcap.php" class="btn btn-secondary mb-3">← Quay lại</a>

    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Mã hàng</th>
                <th>Tên hàng</th>
                <th>Loại hàng</th>
                <th>Giá nhập</th>
                <th>Số lượng</th>
                <th>Đơn vị</th>
                <th>Tồn kho</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($products->num_rows > 0): ?>
                <?php while ($hh = $products->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($hh['mahang']) ?></td>
                        <td><?= htmlspecialchars($hh['tenhang']) ?></td>
                        <td><?= htmlspecialchars($hh['loaihang']) ?></td>
                        <td><?= number_format($hh['giavon'], 0, ',', '.') ?>đ</td>
                        <td><?= $hh['soluong'] ?></td>
                        <td><?= htmlspecialchars($hh['donvitinh']) ?></td>
                        <td><?= $hh['tonkho'] ?></td>
                        <td><?= htmlspecialchars($hh['ghichu'] ?? '') ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center text-muted">Không có sản phẩm.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>