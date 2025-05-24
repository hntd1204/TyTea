<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$result = $conn->query("SELECT * FROM hoadon_anh ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h4>Danh sách hóa đơn (ảnh)</h4>
    <div class="row">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow">
                    <img src="../uploads/<?= $row['ten_file'] ?>" class="card-img-top"
                        style="height: 250px; object-fit: cover;">
                    <div class="card-body">
                        <h6 class="card-title">Tải lên bởi: <?= htmlspecialchars($row['nguoi_tao']) ?></h6>
                        <p class="card-text text-muted"><small><?= $row['ngay_tao'] ?></small></p>
                        <p class="card-text"><?= nl2br(htmlspecialchars($row['ghichu'])) ?></p>
                        <a href="../uploads/<?= $row['ten_file'] ?>" class="btn btn-sm btn-outline-primary"
                            target="_blank">Xem ảnh</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('layout/footer.php'); ?>