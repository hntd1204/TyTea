<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Include phần hiển thị thông báo riêng
include('layout/notification.php');  // Đường dẫn chính xác tùy cấu trúc thư mục của bạn
?>

<div id="page-content-wrapper" class="p-4">
    <!-- Nội dung trang chính -->
    <div class="card shadow-sm border-0 rounded-lg p-4 bg-white" style="max-width: 700px; margin: auto;">
        <div class="text-center mb-3">
            <img src="../img/powder.png" alt="Matcha Tea" width="80" />
        </div>
        <h2 class="text-center text-success font-weight-bold">Chào mừng đến với hệ thống</h2>
        <h4 class="text-center text-primary mb-3">TyTea - Matcha and Tea 🍵</h4>
        <p class="text-center text-muted">
            Hệ thống quản lý bán hàng giúp bạn kiểm soát món, đơn hàng, doanh thu, và nhà cung cấp nhanh chóng và dễ
            dàng.
        </p>
        <div class="text-center mt-4">
            <a href="hanghoa.php" class="btn btn-outline-success btn-lg px-4">
                <i class="fas fa-box mr-2"></i> Quản lý hàng hóa
            </a>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>