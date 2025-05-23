<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

$id = $_GET['id'] ?? 0;
if (!$id) {
    header("Location: khachhang.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ten = $_POST['ten'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $diachi = $_POST['diachi'];

    $conn->query("UPDATE khachhang SET ten='$ten', sdt='$sdt', email='$email', diachi='$diachi' WHERE id=$id");
    header("Location: khachhang.php");
    exit;
}

// Lấy thông tin khách cần sửa
$result = $conn->query("SELECT * FROM khachhang WHERE id = $id");
$row = $result->fetch_assoc();
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Sửa thông tin khách hàng</h2>

    <form method="POST">
        <div class="form-group">
            <label>Họ tên</label>
            <input type="text" name="ten" class="form-control" value="<?= htmlspecialchars($row['ten']) ?>" required>
        </div>
        <div class="form-group">
            <label>Số điện thoại</label>
            <input type="text" name="sdt" class="form-control" value="<?= htmlspecialchars($row['sdt']) ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>">
        </div>
        <div class="form-group">
            <label>Địa chỉ</label>
            <input type="text" name="diachi" class="form-control" value="<?= htmlspecialchars($row['diachi']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="khachhang.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include('layout/footer.php'); ?>