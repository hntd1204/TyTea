<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Xử lý thêm hoặc cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ten'])) {
    $ten = trim($conn->real_escape_string($_POST['ten']));
    $id_update = $_POST['id_update'] ?? null;

    if (!empty($ten)) {
        if ($id_update) {
            // Lấy tên cũ để cập nhật trong bảng hàng hóa
            $old = $conn->query("SELECT ten FROM loaihang WHERE id = $id_update")->fetch_assoc()['ten'];
            $conn->query("UPDATE loaihang SET ten = '$ten' WHERE id = $id_update");
            $conn->query("UPDATE hanghoa SET loaihang = '$ten' WHERE loaihang = '$old'");
        } else {
            $conn->query("INSERT INTO loaihang (ten) VALUES ('$ten')");
        }
        header("Location: loaihang.php");
        exit;
    }
}

// Xử lý xoá
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $ten = $conn->query("SELECT ten FROM loaihang WHERE id = $id")->fetch_assoc()['ten'];
        $conn->query("DELETE FROM loaihang WHERE id = $id");
        // $conn->query("UPDATE hanghoa SET loaihang = '' WHERE loaihang = '$ten'"); // tùy chọn nếu muốn xoá liên kết
        header("Location: loaihang.php");
        exit;
    }
}

// Xử lý tìm kiếm
$search = $_GET['search'] ?? '';
$condition = '';
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $condition = "WHERE ten LIKE '%$s%'";
}

// Dữ liệu sửa
$edit_id = $_GET['edit'] ?? null;
$edit_data = null;
if ($edit_id) {
    $edit_data = $conn->query("SELECT * FROM loaihang WHERE id = $edit_id")->fetch_assoc();
}

// Truy vấn danh sách loại hàng
$result = $conn->query("SELECT * FROM loaihang $condition ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý Loại hàng</h2>

    <!-- Form thêm/sửa -->
    <form method="POST" class="form-inline mb-3">
        <input type="text" name="ten" class="form-control mr-2" placeholder="Tên loại hàng mới" required
            value="<?= htmlspecialchars($edit_data['ten'] ?? '') ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_update" value="<?= $edit_data['id'] ?>">
            <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> Cập nhật</button>
            <a href="loaihang.php" class="btn btn-secondary ml-2">Hủy</a>
        <?php else: ?>
            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
        <?php endif; ?>
    </form>

    <!-- Form tìm kiếm -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm loại hàng..."
            value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary"><i class="fas fa-search"></i> Tìm</button>
    </form>

    <!-- Bảng danh sách -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Tên loại hàng</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id'] ?></td>
                        <td><?= htmlspecialchars($row['ten']) ?></td>
                        <td>
                            <a href="?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Bạn có chắc chắn muốn xoá?')">
                                <i class="fas fa-trash-alt"></i> Xoá
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" class="text-center text-muted">Không tìm thấy kết quả</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>