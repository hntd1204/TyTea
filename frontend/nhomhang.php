<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Thêm nhóm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ten'])) {
    $ten = $conn->real_escape_string($_POST['ten']);
    $conn->query("INSERT INTO nhomhang (ten) VALUES ('$ten')");
    header("Location: nhomhang.php");
    exit;
}

// Xóa nhóm
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM nhomhang WHERE id = $id");
    header("Location: nhomhang.php");
    exit;
}

// Danh sách nhóm hàng
$result = $conn->query("SELECT * FROM nhomhang ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý nhóm hàng</h2>

    <!-- Form thêm -->
    <form method="POST" class="form-inline mb-4">
        <input type="text" name="ten" class="form-control mr-2" placeholder="Tên nhóm hàng..." required>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm nhóm</button>
    </form>

    <!-- Danh sách nhóm hàng -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Tên nhóm</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['ten']) ?></td>
                    <td>
                        <!-- Xoá -->
                        <a href="nhomhang.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xóa nhóm này?');">
                            <i class="fas fa-trash-alt"></i> Xoá
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>