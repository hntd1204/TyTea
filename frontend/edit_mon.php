<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: menu.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenmon = $conn->real_escape_string($_POST['tenmon']);
    $gia = (int)$_POST['gia'];
    $loai = $conn->real_escape_string($_POST['loai']);
    $trangthai = $conn->real_escape_string($_POST['trangthai']);

    $conn->query("UPDATE menu SET tenmon='$tenmon', gia=$gia, loai='$loai', trangthai='$trangthai' WHERE id=$id");
    header("Location: menu.php");
    exit;
}

// Lấy dữ liệu món cần sửa
$result = $conn->query("SELECT * FROM menu WHERE id = $id");
$row = $result->fetch_assoc();

if (!$row) {
    echo "<div class='p-4'>Món không tồn tại.</div>";
    include('layout/footer.php');
    exit;
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Sửa món: <?= htmlspecialchars($row['tenmon']) ?></h2>

    <form method="POST">
        <div class="form-group">
            <label>Mã món (không thay đổi)</label>
            <input type="text" class="form-control" value="<?= htmlspecialchars($row['mamon']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Tên món</label>
            <input type="text" name="tenmon" class="form-control" value="<?= htmlspecialchars($row['tenmon']) ?>"
                required>
        </div>
        <div class="form-group">
            <label>Giá</label>
            <input type="number" name="gia" class="form-control" value="<?= $row['gia'] ?>" required>
        </div>
        <div class="form-group">
            <label>Loại món</label>
            <input type="text" name="loai" class="form-control" value="<?= htmlspecialchars($row['loai']) ?>" required>
        </div>
        <div class="form-group">
            <label>Trạng thái</label>
            <select name="trangthai" class="form-control">
                <option value="Đang bán" <?= $row['trangthai'] == 'Đang bán' ? 'selected' : '' ?>>Đang bán</option>
                <option value="Ngưng bán" <?= $row['trangthai'] == 'Ngưng bán' ? 'selected' : '' ?>>Ngưng bán</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="menu.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include('layout/footer.php'); ?>