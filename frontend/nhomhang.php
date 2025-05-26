<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Thêm hoặc sửa nhóm
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = $conn->real_escape_string($_POST['ten']);
    $id_update = $_POST['id_update'] ?? null;

    if ($id_update) {
        // Lấy tên cũ để cập nhật hàng hóa
        $old = $conn->query("SELECT ten FROM nhomhang WHERE id = $id_update")->fetch_assoc()['ten'];
        $conn->query("UPDATE nhomhang SET ten = '$ten' WHERE id = $id_update");
        $conn->query("UPDATE hanghoa SET nhomhang = '$ten' WHERE nhomhang = '$old'");
    } else {
        $conn->query("INSERT INTO nhomhang (ten) VALUES ('$ten')");
    }
    header("Location: nhomhang.php");
    exit;
}

// Xoá nhóm
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $ten = $conn->query("SELECT ten FROM nhomhang WHERE id = $id")->fetch_assoc()['ten'];
    $conn->query("DELETE FROM nhomhang WHERE id = $id");
    // Optionally: $conn->query("UPDATE hanghoa SET nhomhang = '' WHERE nhomhang = '$ten'");
    header("Location: nhomhang.php");
    exit;
}

// Sửa nhóm
$edit_id = $_GET['edit'] ?? null;
$edit_data = null;
if ($edit_id) {
    $edit_data = $conn->query("SELECT * FROM nhomhang WHERE id = $edit_id")->fetch_assoc();
}

// Danh sách nhóm
$result = $conn->query("SELECT * FROM nhomhang ORDER BY id DESC");

// Danh sách hàng hóa nếu chọn nhóm
$hanghoa = [];
$nhom_ten = '';
if (isset($_GET['nhom'])) {
    $nhom_id = (int) $_GET['nhom'];
    $nhom = $conn->query("SELECT ten FROM nhomhang WHERE id = $nhom_id")->fetch_assoc();
    $nhom_ten = $nhom ? $nhom['ten'] : '';
    $hanghoa_result = $conn->query("SELECT * FROM hanghoa WHERE nhomhang = '{$conn->real_escape_string($nhom_ten)}'");

    while ($hh = $hanghoa_result->fetch_assoc()) {
        $hanghoa[] = $hh;
    }
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý nhóm hàng</h2>

    <!-- Form thêm/sửa nhóm -->
    <form method="POST" class="form-inline mb-4">
        <input type="text" name="ten" class="form-control mr-2" placeholder="Tên nhóm hàng..." required
            value="<?= $edit_data['ten'] ?? '' ?>">
        <?php if ($edit_data): ?>
            <input type="hidden" name="id_update" value="<?= $edit_data['id'] ?>">
            <button type="submit" class="btn btn-warning"><i class="fas fa-edit"></i> Cập nhật</button>
            <a href="nhomhang.php" class="btn btn-secondary ml-2">Hủy</a>
        <?php else: ?>
            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm nhóm</button>
        <?php endif; ?>
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
                    <td>
                        <a href="nhomhang.php?nhom=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['ten']) ?>
                        </a>
                    </td>
                    <td>
                        <a href="nhomhang.php?edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <a href="nhomhang.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xóa nhóm này?');">
                            <i class="fas fa-trash-alt"></i> Xóa
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Hàng hóa thuộc nhóm -->
    <?php if (!empty($hanghoa)): ?>
        <h4 class="mt-5">📦 Hàng hóa thuộc nhóm: <span class="text-primary"><?= htmlspecialchars($nhom_ten) ?></span></h4>
        <table class="table table-striped table-bordered mt-3">
            <thead>
                <tr>
                    <th>Mã hàng</th>
                    <th>Tên hàng</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hanghoa as $hh): ?>
                    <tr>
                        <td><?= htmlspecialchars($hh['mahang']) ?></td>
                        <td><?= htmlspecialchars($hh['tenhang']) ?></td>
                        <td><?= number_format($hh['giaban']) ?> đ</td>
                        <td><?= $hh['tonkho'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include('layout/footer.php'); ?>