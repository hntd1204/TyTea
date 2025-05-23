<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include('layout/header.php');
include('layout/sidebar.php');
?>

<!-- Nội dung chính -->
<div id="page-content-wrapper" class="p-4">
    <h2>Chào mừng đến với hệ thống quản lý bán hàng</h2>
    <p>Vui lòng chọn chức năng từ menu bên trái để bắt đầu sử dụng.</p>
</div>

<?php include('layout/footer.php'); ?>