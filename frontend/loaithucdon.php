<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Xử lý thêm hoặc cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ten'])) {
    $ten = trim($conn->real_escape_string($_POST['ten']));
    $id_update = $_POST['id_update'] ?? null;

    if (!empty($ten)) {
        try {
            // Nếu đang chỉnh sửa, cập nhật loại thực đơn
            if ($id_update) {
                $conn->query("UPDATE loaithucdon SET ten = '$ten' WHERE id = $id_update");
            } else {
                // Thêm loại thực đơn mới
                $conn->query("INSERT INTO loaithucdon (ten) VALUES ('$ten')");
            }

            // Redirect về trang loaithucdon.php sau khi cập nhật hoặc thêm mới
            header("Location: loaithucdon.php");
            exit; // Đảm bảo không có mã nào được thực thi sau redirect
        } catch (Exception $e) {
            echo 'Lỗi: ' . $e->getMessage();
        }
    }
}

// Xoá
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM loaithucdon WHERE id = $id");
    header("Location: loaithucdon.php");
    exit; // Đảm bảo không có mã nào được thực thi sau khi xóa
}

// Lấy loại cần sửa
$edit_id = $_GET['edit'] ?? null;
$edit_data = null;
if ($edit_id) {
    $edit_data = $conn->query("SELECT * FROM loaithucdon WHERE id = $edit_id")->fetch_assoc();
}

// Danh sách loại thực đơn
$result = $conn->query("SELECT * FROM loaithucdon ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý Loại thực đơn</h2>

    <!-- Form thêm / sửa -->
    <form method="POST" class="form-inline mb-3">
        <div class="form-group">
            <label for="ten" class="mr-2">Tên loại thực đơn:</label>
            <input type="text" name="ten" class="form-control mr-2" placeholder="Tên loại thực đơn"
                value="<?= htmlspecialchars($edit_data['ten'] ?? '') ?>" required>
        </div>
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_update" value="<?= $edit_data['id'] ?>">
            <button class="btn btn-warning"><i class="fas fa-edit"></i> Cập nhật</button>
            <a href="loaithucdon.php" class="btn btn-secondary ml-2">Hủy</a>
        <?php else: ?>
            <button class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
        <?php endif; ?>
    </form>

    <!-- Danh sách -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Tên loại</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['ten']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id'] ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Xoá loại thực đơn này?')">
                            <i class="fas fa-trash-alt"></i> Xoá
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>