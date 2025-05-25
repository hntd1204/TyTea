<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$ncc_list = $conn->query("SELECT DISTINCT nhacungcap FROM hanghoa WHERE nhacungcap IS NOT NULL AND nhacungcap <> '' ORDER BY nhacungcap ASC");
?>

<div id="page-content-wrapper" class="p-4">
    <h4>📤 Tải lên hóa đơn (ảnh)</h4>
    <form method="POST" action="save_hoadon_anh.php" enctype="multipart/form-data">
        <div class="form-group">
            <label>Chọn ảnh hóa đơn:</label>
            <input type="file" name="anh" class="form-control-file" required accept="image/*">
        </div>

        <div class="form-row">
            <div class="col">
                <input name="nguoi_tao" class="form-control" placeholder="Người tạo" required>
            </div>
            <div class="col">
                <input name="ghichu" class="form-control" placeholder="Ghi chú (tuỳ chọn)">
            </div>
        </div>

        <div class="form-row mt-2">
            <div class="col">
                <label>Nhà cung cấp</label>
                <select name="nhacungcap" class="form-control" required>
                    <option value="">-- Chọn nhà cung cấp --</option>
                    <?php while ($ncc = $ncc_list->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($ncc['nhacungcap']) ?>">
                            <?= htmlspecialchars($ncc['nhacungcap']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
        </div>

        <button class="btn btn-primary mt-3">Lưu hóa đơn</button>
    </form>
</div>

<?php include('layout/footer.php'); ?>