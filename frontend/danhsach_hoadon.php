<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$filter_ncc = $_GET['filter_ncc'] ?? '';
$ncc_list = $conn->query("SELECT DISTINCT nhacungcap FROM hoadon_anh WHERE nhacungcap IS NOT NULL AND nhacungcap <> '' ORDER BY nhacungcap ASC");

$cond = '';
if (!empty($filter_ncc)) {
    $safe_ncc = $conn->real_escape_string($filter_ncc);
    $cond = "WHERE nhacungcap = '$safe_ncc'";
}
$result = $conn->query("SELECT * FROM hoadon_anh $cond ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h4>🖼️ Hóa đơn đã lưu</h4>

    <!-- Bộ lọc -->
    <form method="GET" class="form-inline mb-3">
        <label class="mr-2">Lọc theo nhà cung cấp:</label>
        <select name="filter_ncc" class="form-control mr-2">
            <option value="">-- Tất cả --</option>
            <?php while ($ncc = $ncc_list->fetch_assoc()): ?>
                <?php $selected = ($ncc['nhacungcap'] == $filter_ncc) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($ncc['nhacungcap']) ?>" <?= $selected ?>>
                    <?= htmlspecialchars($ncc['nhacungcap']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button class="btn btn-outline-primary">Lọc</button>
    </form>

    <!-- Danh sách hóa đơn -->
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <img src="../uploads/<?= htmlspecialchars($row['ten_file']) ?>" class="card-img-top"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title">Người tạo: <?= htmlspecialchars($row['nguoi_tao']) ?></h6>
                        <p class="mb-1"><strong>Nhà cung cấp:</strong> <?= htmlspecialchars($row['nhacungcap']) ?></p>
                        <p class="text-muted"><small><?= date('d/m/Y H:i', strtotime($row['ngay_tao'])) ?></small></p>
                        <p><?= nl2br(htmlspecialchars($row['ghichu'])) ?></p>
                        <a href="../uploads/<?= htmlspecialchars($row['ten_file']) ?>"
                            class="btn btn-sm btn-outline-primary" target="_blank">Xem ảnh</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>