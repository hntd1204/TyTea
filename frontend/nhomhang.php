<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

// Xử lý thêm nhóm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ten'])) {
    $ten = $conn->real_escape_string($_POST['ten']);
    $conn->query("INSERT INTO nhomhang (ten) VALUES ('$ten')");
    header("Location: nhomhang.php");
    exit;
}

// Xử lý xóa nhóm
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM nhomhang WHERE id = $id");
    header("Location: nhomhang.php");
    exit;
}

// Lấy danh sách nhóm hàng
$result = $conn->query("SELECT * FROM nhomhang ORDER BY id DESC");

// Lấy danh sách hàng hóa nếu chọn nhóm
$hanghoa = [];
$nhom_ten = '';
if (isset($_GET['nhom'])) {
    $nhom_id = (int) $_GET['nhom'];
    $nhom = $conn->query("SELECT ten FROM nhomhang WHERE id = $nhom_id")->fetch_assoc();
    $nhom_ten = $nhom ? $nhom['ten'] : '';
    $hanghoa_result = $conn->query("SELECT * FROM hanghoa WHERE nhomhang_id = $nhom_id");

    while ($hh = $hanghoa_result->fetch_assoc()) {
        $hanghoa[] = $hh;
    }
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý nhóm hàng</h2>

    <!-- Form thêm nhóm -->
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
                    <td>
                        <a href="nhomhang.php?nhom=<?= $row['id'] ?>">
                            <?= htmlspecialchars($row['ten']) ?>
                        </a>
                    </td>
                    <td>
                        <a href="nhomhang.php?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xóa nhóm này?');">
                            <i class="fas fa-trash-alt"></i> Xóa
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Danh sách hàng hóa thuộc nhóm được chọn -->
    <?php if (!empty($hanghoa)): ?>
        <h4 class="mt-5">Danh sách hàng hóa thuộc nhóm: <?= htmlspecialchars($nhom_ten) ?></h4>
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