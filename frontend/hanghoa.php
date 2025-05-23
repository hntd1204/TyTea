<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
// Xử lý thêm hàng hóa mới
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mahang'])) {
    $mahang = $_POST['mahang'];
    $tenhang = $_POST['tenhang'];
    $loaihang = $_POST['loaihang'];
    $giaban = $_POST['giaban'];
    $tonkho = $_POST['tonkho'];
    $donvitinh = $_POST['donvitinh'];
    $nhacungcap = $_POST['nhacungcap'] ?? '';

    $sql = "INSERT INTO hanghoa (mahang, tenhang, loaihang, giaban, tonkho, donvitinh, nhacungcap)
            VALUES ('$mahang', '$tenhang', '$loaihang', '$giaban', '$tonkho', '$donvitinh', '$nhacungcap')";
    $conn->query($sql);
    header("Location: hanghoa.php");
    exit;
}

// Xử lý nhập thêm tồn kho
if (isset($_POST['nhap_id']) && isset($_POST['so_luong_nhap'])) {
    $id = (int) $_POST['nhap_id'];
    $sl = (int) $_POST['so_luong_nhap'];
    if ($sl > 0) {
        $conn->query("UPDATE hanghoa SET tonkho = tonkho + $sl WHERE id = $id");
    }
    header("Location: hanghoa.php");
    exit;
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Danh sách hàng hóa</h2>

    <!-- Tìm kiếm -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" class="form-inline">
            <input type="text" name="search" class="form-control mr-2" placeholder="Tìm theo mã hoặc tên..."
                value="<?= $_GET['search'] ?? '' ?>">
            <button type="submit" class="btn btn-outline-primary">Tìm</button>
        </form>
    </div>

    <!-- Form thêm hàng mới -->
    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="col"><input name="mahang" class="form-control" placeholder="Mã hàng" required></div>
            <div class="col"><input name="tenhang" class="form-control" placeholder="Tên hàng" required></div>
            <div class="col"><input name="loaihang" class="form-control" placeholder="Loại hàng"></div>
            <div class="col"><input type="number" name="giaban" class="form-control" placeholder="Giá nhập" required>
            </div>
            <div class="col"><input type="number" name="tonkho" class="form-control" placeholder="Tồn kho" required>
            </div>
            <div class="col"><input name="donvitinh" class="form-control" placeholder="Đơn vị (ví dụ: kg, gói)"
                    required></div>
            <div class="col"><input name="nhacungcap" class="form-control" placeholder="Nhà cung cấp"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
            </div>
        </div>
    </form>

    <!-- Bảng danh sách hàng hóa -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Mã hàng</th>
                <th>Tên hàng</th>
                <th>Loại</th>
                <th>Giá nhập</th>
                <th>Tồn kho</th>
                <th>Đơn vị</th>
                <th>Nhà cung cấp</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $search = $_GET['search'] ?? '';
            $search_sql = "";

            if (!empty($search)) {
                $search = $conn->real_escape_string($search);
                $search_sql = "WHERE mahang LIKE '%$search%' OR tenhang LIKE '%$search%'";
            }

            $result = $conn->query("SELECT * FROM hanghoa $search_sql ORDER BY id DESC");
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['mahang']}</td>
                    <td>{$row['tenhang']}</td>
                    <td>{$row['loaihang']}</td>
                    <td>" . number_format($row['giaban'], 0, ',', '.') . "đ</td>
                    <td>{$row['tonkho']}</td>
                    <td>{$row['donvitinh']}</td>
                    <td>{$row['nhacungcap']}</td>
                    <td>
                        <a href='edit_hanghoa.php?id={$row['id']}' class='btn btn-sm btn-primary'>
                            <i class='fas fa-edit'></i> Sửa
                        </a>
                        <a href='../backend/delete_hanghoa.php?id={$row['id']}' class='btn btn-sm btn-danger'
                           onclick='return confirm(\"Xác nhận xóa?\");'>
                           <i class='fas fa-trash-alt'></i> Xóa
                        </a>
                    </td>
                </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- Nhập thêm hàng -->
    <h4 class="mt-5 mb-3">Nhập thêm hàng vào kho</h4>
    <form method="POST" class="form-inline">
        <select name="nhap_id" class="form-control mr-2" required>
            <option value="">-- Chọn hàng --</option>
            <?php
            $list = $conn->query("SELECT id, tenhang, mahang FROM hanghoa");
            while ($item = $list->fetch_assoc()) {
                echo "<option value='{$item['id']}'>{$item['tenhang']} ({$item['mahang']})</option>";
            }
            ?>
        </select>
        <input type="number" name="so_luong_nhap" class="form-control mr-2" placeholder="Số lượng nhập" min="1"
            required>
        <button type="submit" class="btn btn-primary"><i class="fas fa-download"></i> Nhập hàng</button>
    </form>
</div>

<?php include('layout/footer.php'); ?>