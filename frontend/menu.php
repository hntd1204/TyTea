<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
// Xử lý thêm món mới
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $mamon = $_POST['mamon'];
    $tenmon = $_POST['tenmon'];
    $gia = $_POST['gia'];
    $loai = $_POST['loai'];
    $trangthai = $_POST['trangthai'];

    $sql = "INSERT INTO menu (mamon, tenmon, gia, loai, trangthai)
            VALUES ('$mamon', '$tenmon', '$gia', '$loai', '$trangthai')";
    $conn->query($sql);
    header("Location: menu.php");
    exit;
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Danh sách món / menu</h2>

    <!-- Form thêm món -->
    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="col"><input name="mamon" class="form-control" placeholder="Mã món" required></div>
            <div class="col"><input name="tenmon" class="form-control" placeholder="Tên món" required></div>
            <div class="col"><input type="number" name="gia" class="form-control" placeholder="Giá" required></div>
            <div class="col">
                <input type="text" name="loai" class="form-control" placeholder="Nhập loại món" required>
            </div>
            <div class="col">
                <select name="trangthai" class="form-control">
                    <option value="Đang bán">Đang bán</option>
                    <option value="Ngưng bán">Ngưng bán</option>
                </select>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm món</button>
            </div>
        </div>
    </form>

    <!-- Bảng danh sách món -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Mã món</th>
                <th>Tên món</th>
                <th>Giá</th>
                <th>Loại</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $result = $conn->query("SELECT * FROM menu ORDER BY id DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['mamon']}</td>
                        <td>{$row['tenmon']}</td>
                        <td>" . number_format($row['gia'], 0, ',', '.') . "đ</td>
                        <td>{$row['loai']}</td>
                        <td>" . htmlspecialchars($row['trangthai']) . "</td>
                        <td>
                            <a href='edit_mon.php?id={$row['id']}' class='btn btn-sm btn-primary'>
                                <i class='fas fa-edit'></i> Sửa
                            </a>
                            <a href='../backend/delete_mon.php?id={$row['id']}' class='btn btn-sm btn-danger'
                               onclick='return confirm(\"Xác nhận xóa?\");'>
                               <i class='fas fa-trash-alt'></i> Xóa
                            </a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>