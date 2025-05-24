<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Xử lý thêm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ten'])) {
    $ten = trim($conn->real_escape_string($_POST['ten']));
    if (!empty($ten)) {
        $conn->query("INSERT INTO loaihang (ten) VALUES ('$ten')");
        header("Location: loaihang.php");
        exit;
    }
}

// Xử lý xoá
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id > 0) {
        $conn->query("DELETE FROM loaihang WHERE id = $id");
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

// Truy vấn danh sách loại hàng
$result = $conn->query("SELECT * FROM loaihang $condition ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý Loại hàng</h2>

    <!-- Form thêm -->
    <form method="POST" class="form-inline mb-3">
        <input type="text" name="ten" class="form-control mr-2" placeholder="Tên loại hàng mới" required>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
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