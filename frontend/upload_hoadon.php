<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>

<div id="page-content-wrapper" class="p-4">
    <h4>Upload ảnh hóa đơn</h4>
    <form method="POST" action="../backend/save_hoadon_anh.php" enctype="multipart/form-data">
        <div class="form-group">
            <label>Chọn ảnh hóa đơn:</label>
            <input type="file" name="anh" class="form-control-file" required accept="image/*">
        </div>
        <div class="form-row">
            <div class="col"><input name="nguoi_tao" class="form-control" placeholder="Người tạo" required></div>
            <div class="col"><input name="ghichu" class="form-control" placeholder="Ghi chú"></div>
        </div>
        <button class="btn btn-primary mt-3">Lưu hóa đơn</button>
    </form>
</div>

<?php include('layout/footer.php'); ?>