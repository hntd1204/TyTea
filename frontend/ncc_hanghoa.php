<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

$ncc = $_GET['ncc'] ?? '';
if (!$ncc) {
    header("Location: nhacungcap.php");
    exit;
}

$ncc_sql = $conn->real_escape_string($ncc);
$hangs = $conn->query("SELECT * FROM hanghoa WHERE nhacungcap = '$ncc_sql'");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Danh sách hàng của nhà cung cấp: <strong><?= htmlspecialchars($ncc) ?></strong></h2>

    <a href="nhacungcap.php" class="btn btn-secondary mb-3">← Quay lại</a>

    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Mã hàng</th>
                <th>Tên hàng</th>
                <th>Loại</th>
                <th>Giá nhập</th>
                <th>Tồn kho</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $hangs->fetch_assoc()): ?>
            <tr>
                <td><?= $row['mahang'] ?></td>
                <td><?= $row['tenhang'] ?></td>
                <td><?= $row['loaihang'] ?></td>
                <td><?= number_format($row['giaban'], 0, ',', '.') ?>đ</td>
                <td><?= $row['tonkho'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>