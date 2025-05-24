<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$result = $conn->query("SELECT * FROM hoadon_anh ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h4>🖼️ Hóa đơn đã lưu</h4>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-4 mb-4">
            <div class="card shadow">
                <img src="../uploads/<?= $row['ten_file'] ?>" class="card-img-top"
                    style="height: 250px; object-fit: cover;">
                <div class="card-body">
                    <h6 class="card-title">Người tạo: <?= htmlspecialchars($row['nguoi_tao']) ?></h6>
                    <p class="text-muted"><small><?= date('d/m/Y H:i', strtotime($row['ngay_tao'])) ?></small></p>
                    <p><?= nl2br(htmlspecialchars($row['ghichu'])) ?></p>
                    <a href="../uploads/<?= $row['ten_file'] ?>" class="btn btn-sm btn-outline-primary"
                        target="_blank">Xem ảnh</a>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>