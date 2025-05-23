<?php
include('db_connect.php');

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Kiểm tra hàng hóa có đang được sử dụng không
    $check = $conn->query("SELECT COUNT(*) as total FROM chitiethoadon WHERE hanghoa_id = $id")->fetch_assoc()['total'];

    if ($check > 0) {
        echo "<script>
            alert('Không thể xóa. Sản phẩm đang tồn tại trong hóa đơn.');
            window.location.href = '../frontend/hanghoa.php';
        </script>";
        exit;
    }

    $conn->query("DELETE FROM hanghoa WHERE id = $id");
}

header("Location: ../frontend/hanghoa.php");
exit;