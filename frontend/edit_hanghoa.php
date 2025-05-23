<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Lấy ID từ URL
$id = $_GET['id'] ?? 0;
if ($id == 0) {
    header("Location: hanghoa.php");
    exit;
}

// Xử lý cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenhang = $_POST['tenhang'];
    $loaihang = $_POST['loaihang'];
    $giaban = $_POST['giaban'];
    $tonkho = $_POST['tonkho'];
    $nhacungcap = $_POST['nhacungcap'];

    $sql = "UPDATE hanghoa 
            SET tenhang='$tenhang', loaihang='$loaihang', giaban='$giaban', tonkho='$tonkho', nhacungcap='$nhacungcap' 
            WHERE id=$id";
    $conn->query($sql);
    header("Location: hanghoa.php");
    exit;
}

// Lấy dữ liệu hiện tại
$result = $conn->query("SELECT * FROM hanghoa WHERE id = $id");
$row = $result->fetch_assoc();
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Cập nhật hàng hóa</h2>
    <form method="POST">
        <div class="form-group">
            <label>Mã hàng (không sửa)</label>
            <input type="text" class="form-control" value="<?= $row['mahang'] ?>" disabled>
        </div>
        <div class="form-group">
            <label>Tên hàng</label>
            <input type="text" name="tenhang" class="form-control" value="<?= $row['tenhang'] ?>" required>
        </div>
        <div class="form-group">
            <label>Loại hàng</label>
            <input type="text" name="loaihang" class="form-control" value="<?= $row['loaihang'] ?>">
        </div>
        <div class="form-group">
            <label>Giá bán</label>
            <input type="number" name="giaban" class="form-control" value="<?= $row['giaban'] ?>" required>
        </div>
        <div class="form-group">
            <label>Tồn kho</label>
            <input type="number" name="tonkho" class="form-control" value="<?= $row['tonkho'] ?>" required>
        </div>
        <div class="form-group">
            <label>Nhà cung cấp</label>
            <input type="text" name="nhacungcap" class="form-control" value="<?= $row['nhacungcap'] ?>">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="hanghoa.php" class="btn btn-secondary">Hủy</a>
    </form>
</div>

<?php include('layout/footer.php'); ?>