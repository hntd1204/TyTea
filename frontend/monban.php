<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>
<?php // Include phần hiển thị thông báo riêng
include('layout/notification.php');  // Đường dẫn chính xác tùy cấu trúc thư mục của bạn
?>

<?php
// Danh sách loại thực đơn
$ds_loai = $conn->query("SELECT ten FROM loaithucdon ORDER BY ten ASC");

// Lấy trang hiện tại (mặc định 1)
$page = $_GET['page'] ?? 1;

// Xử lý thêm / cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ten = trim($_POST['ten_mon'] ?? '');
    $loai = $_POST['loai'] ?? '';
    $id_update = $_POST['id_update'] ?? '';
    $giavon_500 = (int)($_POST['giavon_500'] ?? 0);
    $giavon_700 = (int)($_POST['giavon_700'] ?? 0);
    $giaban_500 = (int)($_POST['giaban_500'] ?? 0);
    $giaban_700 = (int)($_POST['giaban_700'] ?? 0);
    $ghichu = $conn->real_escape_string($_POST['ghichu'] ?? '');
    $page = $_POST['page'] ?? 1;

    if ($ten !== '') {
        if ($id_update) {
            $sql = "UPDATE monban SET 
                        ten='$ten', loaithucdon='$loai',
                        giavon_500=$giavon_500, giavon_700=$giavon_700,
                        giaban_500=$giaban_500, giaban_700=$giaban_700,
                        ghichu='$ghichu'
                    WHERE id=$id_update";
        } else {
            $sql = "INSERT INTO monban (ten, loaithucdon, giavon_500, giavon_700, giaban_500, giaban_700, ghichu)
                    VALUES ('$ten', '$loai', $giavon_500, $giavon_700, $giaban_500, $giaban_700, '$ghichu')";
        }
        $conn->query($sql);
        header("Location: monban.php?page=" . intval($page));
        exit;
    }
}

// Xóa món
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM monban WHERE id = $id");
    header("Location: monban.php?page=" . intval($page));
    exit;
}

// Tìm kiếm + lọc
$filter_loai = $_GET['loai'] ?? '';
$search = $_GET['search'] ?? '';
$conds = [];

if (!empty($filter_loai)) {
    $conds[] = "loaithucdon = '" . $conn->real_escape_string($filter_loai) . "'";
}
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $conds[] = "ten LIKE '%$s%'";
}
$where = $conds ? "WHERE " . implode(" AND ", $conds) : "";

// Phân trang
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;
$count_result = $conn->query("SELECT COUNT(*) AS total FROM monban $where");
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Dữ liệu món
$ds_mon = $conn->query("SELECT * FROM monban $where ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Dữ liệu khi sửa
$mon_sua = null;
if (isset($_GET['edit'])) {
    $id = (int) $_GET['edit'];
    $mon_sua = $conn->query("SELECT * FROM monban WHERE id=$id")->fetch_assoc();
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý món đang bán</h2>

    <!-- Form thêm / sửa -->
    <form method="POST" class="mb-4">
        <input type="hidden" name="page" value="<?= htmlspecialchars($page) ?>">
        <div class="form-row mb-2">
            <div class="col"><input name="ten_mon" class="form-control" placeholder="Tên món" required
                    value="<?= $mon_sua['ten'] ?? '' ?>"></div>
            <div class="col">
                <select name="loai" class="form-control" required>
                    <option value="">-- Loại thực đơn --</option>
                    <?php $ds_loai->data_seek(0);
                    while ($l = $ds_loai->fetch_assoc()):
                        $selected = ($mon_sua['loaithucdon'] ?? '') == $l['ten'] ? 'selected' : '';
                        echo "<option value='{$l['ten']}' $selected>{$l['ten']}</option>";
                    endwhile; ?>
                </select>
            </div>
        </div>

        <div class="form-row mb-2">
            <div class="col"><input name="giavon_500" type="number" class="form-control" placeholder="Giá vốn 500ml"
                    value="<?= $mon_sua['giavon_500'] ?? '' ?>"></div>
            <div class="col"><input name="giavon_700" type="number" class="form-control" placeholder="Giá vốn 700ml"
                    value="<?= $mon_sua['giavon_700'] ?? '' ?>"></div>
        </div>
        <div class="form-row mb-2">
            <div class="col"><input name="giaban_500" type="number" class="form-control" placeholder="Giá bán 500ml"
                    value="<?= $mon_sua['giaban_500'] ?? '' ?>"></div>
            <div class="col"><input name="giaban_700" type="number" class="form-control" placeholder="Giá bán 700ml"
                    value="<?= $mon_sua['giaban_700'] ?? '' ?>"></div>
        </div>
        <div class="form-group">
            <textarea name="ghichu" class="form-control"
                placeholder="Ghi chú"><?= $mon_sua['ghichu'] ?? '' ?></textarea>
        </div>

        <?php if ($mon_sua): ?>
            <input type="hidden" name="id_update" value="<?= $mon_sua['id'] ?>">
            <button class="btn btn-warning">Cập nhật</button>
            <a href="monban.php?page=<?= intval($page) ?>" class="btn btn-secondary ml-2">Hủy</a>
        <?php else: ?>
            <button class="btn btn-success">Thêm món</button>
        <?php endif; ?>
    </form>

    <!-- Tìm kiếm + lọc -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm tên món..."
            value="<?= htmlspecialchars($search) ?>">
        <select name="loai" class="form-control mr-2">
            <option value="">-- Tất cả loại thực đơn --</option>
            <?php $ds_loai->data_seek(0);
            while ($l = $ds_loai->fetch_assoc()):
                $selected = $filter_loai == $l['ten'] ? 'selected' : '';
                echo "<option value='{$l['ten']}' $selected>{$l['ten']}</option>";
            endwhile; ?>
        </select>
        <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Lọc</button>
    </form>

    <!-- Danh sách món -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Tên món</th>
                <th>Loại TD</th>
                <th>Giá vốn<br>500ml</th>
                <th>Giá vốn<br>700ml</th>
                <th>Giá bán<br>500ml</th>
                <th>Giá bán<br>700ml</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($m = $ds_mon->fetch_assoc()): ?>
                <tr>
                    <td><?= $m['id'] ?></td>
                    <td><?= htmlspecialchars($m['ten']) ?></td>
                    <td><?= htmlspecialchars($m['loaithucdon']) ?></td>
                    <td><?= number_format($m['giavon_500']) ?></td>
                    <td><?= number_format($m['giavon_700']) ?></td>
                    <td><?= number_format($m['giaban_500']) ?></td>
                    <td><?= number_format($m['giaban_700']) ?></td>
                    <td><?= nl2br(htmlspecialchars($m['ghichu'])) ?></td>
                    <td>
                        <a href="congthuc.php?mon_id=<?= $m['id'] ?>" class="btn btn-sm btn-info">Công thức</a>
                        <a href="?edit=<?= $m['id'] ?>&page=<?= intval($page) ?>" class="btn btn-sm btn-primary">Sửa</a>
                        <a href="?delete=<?= $m['id'] ?>&page=<?= intval($page) ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xoá?')">Xoá</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Phân trang -->
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<?php include('layout/footer.php'); ?>