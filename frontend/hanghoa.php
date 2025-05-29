<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');
// Include phần hiển thị thông báo riêng
include('layout/notification.php');  // Đường dẫn chính xác tùy cấu trúc thư mục của bạn
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Lấy trang hiện tại (mặc định 1)
$page = max(1, (int)($_GET['page'] ?? 1));

// Xử lý thêm mới hoặc cập nhật hàng hóa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mahang'])) {
    $mahang = $conn->real_escape_string($_POST['mahang']);
    $tenhang = $conn->real_escape_string($_POST['tenhang']);
    $giavon = (float)$_POST['giavon'];
    $loaihang = $conn->real_escape_string($_POST['loaihang']);
    $nhomhang = $conn->real_escape_string($_POST['nhomhang']);
    $soluong = (int)$_POST['soluong'];
    $donvitinh = $conn->real_escape_string($_POST['donvitinh']);
    $tonkho = (int)$_POST['tonkho'];
    $nhacungcap = $conn->real_escape_string($_POST['nhacungcap']);
    $ghichu = $conn->real_escape_string($_POST['ghichu']);
    $page_post = max(1, (int)($_POST['page'] ?? $page));

    if (!empty($_POST['id_update'])) {
        // Cập nhật
        $id_update = (int)$_POST['id_update'];
        $sql = "UPDATE hanghoa SET 
            mahang='$mahang', tenhang='$tenhang', giavon=$giavon,
            loaihang='$loaihang', nhomhang='$nhomhang', soluong=$soluong,
            donvitinh='$donvitinh', tonkho=$tonkho, nhacungcap='$nhacungcap',
            ghichu='$ghichu'
            WHERE id=$id_update";
    } else {
        // Thêm mới
        $sql = "INSERT INTO hanghoa 
            (mahang, tenhang, giavon, loaihang, nhomhang, soluong, donvitinh, tonkho, nhacungcap, ghichu)
            VALUES 
            ('$mahang', '$tenhang', $giavon, '$loaihang', '$nhomhang', $soluong, '$donvitinh', $tonkho, '$nhacungcap', '$ghichu')";
    }

    $conn->query($sql);

    // Redirect về trang hiện tại để giữ vị trí phân trang
    header("Location: hanghoa.php?page=$page_post");
    exit;
}

// Lọc & phân trang
$search = $_GET['search'] ?? '';
$filter_nhom = $_GET['filter_nhom'] ?? '';
$filter_loai = $_GET['filter_loai'] ?? '';
$limit = 10;
$offset = ($page - 1) * $limit;

$cond = [];
if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $cond[] = "(mahang LIKE '%$s%' OR tenhang LIKE '%$s%' OR nhacungcap LIKE '%$s%')";
}
if (!empty($filter_nhom)) {
    $f = $conn->real_escape_string($filter_nhom);
    $cond[] = "nhomhang = '$f'";
}
if (!empty($filter_loai)) {
    $l = $conn->real_escape_string($filter_loai);
    $cond[] = "loaihang = '$l'";
}
$where = count($cond) ? "WHERE " . implode(" AND ", $cond) : "";

$countRes = $conn->query("SELECT COUNT(*) as total FROM hanghoa $where");
$totalRows = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

$result = $conn->query("SELECT * FROM hanghoa $where ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Lấy danh sách lọc cho form và modal
$ds_loai_arr = [];
$ds_loai_res = $conn->query("SELECT ten FROM loaihang ORDER BY ten ASC");
while ($row = $ds_loai_res->fetch_assoc()) {
    $ds_loai_arr[] = $row['ten'];
}

$ds_nhom_arr = [];
$ds_nhom_res = $conn->query("SELECT ten FROM nhomhang ORDER BY ten ASC");
while ($row = $ds_nhom_res->fetch_assoc()) {
    $ds_nhom_arr[] = $row['ten'];
}

// Tính tổng tiền nhập (giá vốn * số lượng) theo điều kiện lọc
$sumRes = $conn->query("SELECT SUM(giavon * soluong) AS tongtien FROM hanghoa $where");
$tongtien = $sumRes->fetch_assoc()['tongtien'] ?? 0;

// Lấy thông tin để sửa (nếu có)
$item_sua = null;
if (isset($_GET['edit'])) {
    $id_edit = (int)$_GET['edit'];
    $item_sua = $conn->query("SELECT * FROM hanghoa WHERE id = $id_edit")->fetch_assoc();
}
?>

<div id="page-content-wrapper" class="p-4">
    <div class="d-flex justify-content-between mb-3 align-items-center flex-wrap">
        <h2>Quản Lý Hàng Hóa</h2>
        <div>
            <button class="btn btn-success" data-toggle="modal" data-target="#themModal">
                <i class="fas fa-plus"></i> Thêm mới
            </button>
            <a href="export_excel.php?<?= http_build_query($_GET) ?>" class="btn btn-info ml-2">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
        </div>
    </div>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm theo mã, tên, NCC..."
            value="<?= htmlspecialchars($search) ?>">
        <select name="filter_nhom" class="form-control mr-2">
            <option value="">-- Nhóm hàng --</option>
            <?php foreach ($ds_nhom_arr as $nh): ?>
                <option value="<?= htmlspecialchars($nh) ?>" <?= ($filter_nhom == $nh) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($nh) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="filter_loai" class="form-control mr-2">
            <option value="">-- Loại hàng --</option>
            <?php foreach ($ds_loai_arr as $lh): ?>
                <option value="<?= htmlspecialchars($lh) ?>" <?= ($filter_loai == $lh) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($lh) ?></option>
            <?php endforeach; ?>
        </select>
        <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Lọc</button>
    </form>

    <div class="text-right mb-2 font-weight-bold">
        Tổng tiền nhập: <span class="text-danger"><?= number_format($tongtien, 0, ',', '.') ?> đ</span>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Mã hàng</th>
                    <th>Tên hàng</th>
                    <th>Giá nhập</th>
                    <th>Loại</th>
                    <th>Nhóm</th>
                    <th>Số lượng</th>
                    <th>Đơn vị</th>
                    <th>Tồn kho</th>
                    <th>Nhà cung cấp</th>
                    <th>Ghi chú</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['mahang']) ?></td>
                        <td><?= htmlspecialchars($row['tenhang']) ?></td>
                        <td><?= number_format($row['giavon'], 0, ',', '.') ?> đ</td>
                        <td><?= htmlspecialchars($row['loaihang']) ?></td>
                        <td><?= htmlspecialchars($row['nhomhang']) ?></td>
                        <td><?= $row['soluong'] ?></td>
                        <td><?= htmlspecialchars($row['donvitinh']) ?></td>
                        <td><?= $row['tonkho'] ?></td>
                        <td><?= htmlspecialchars($row['nhacungcap']) ?></td>
                        <td><?= htmlspecialchars($row['ghichu'] ?? '') ?></td>
                        <td>
                            <a href="hanghoa.php?edit=<?= $row['id'] ?>&page=<?= $page ?>"
                                class="btn btn-sm btn-primary">Sửa</a>
                            <a href="../backend/delete_hanghoa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Modal thêm / sửa -->
<div class="modal fade" id="themModal" tabindex="-1" role="dialog" aria-labelledby="themModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <input type="hidden" name="page" value="<?= htmlspecialchars($page) ?>">
            <?php if ($item_sua): ?>
                <input type="hidden" name="id_update" value="<?= $item_sua['id'] ?>">
            <?php endif; ?>
            <div class="modal-header">
                <h5 class="modal-title" id="themModalLabel"><?= $item_sua ? "Sửa hàng hóa" : "Thêm hàng hóa" ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Đóng">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-row mb-2">
                    <div class="col"><label>Mã hàng hóa</label><input name="mahang" class="form-control" required
                            value="<?= htmlspecialchars($item_sua['mahang'] ?? '') ?>"></div>
                    <div class="col"><label>Tên hàng hóa</label><input name="tenhang" class="form-control" required
                            value="<?= htmlspecialchars($item_sua['tenhang'] ?? '') ?>"></div>
                </div>
                <div class="form-row mb-2">
                    <div class="col"><label>Giá nhập</label><input name="giavon" type="number" step="0.01"
                            class="form-control" required value="<?= htmlspecialchars($item_sua['giavon'] ?? '') ?>">
                    </div>
                    <div class="col"><label>Loại hàng</label>
                        <select name="loaihang" class="form-control">
                            <?php foreach ($ds_loai_arr as $l): ?>
                                <option value="<?= htmlspecialchars($l) ?>"
                                    <?= (isset($item_sua['loaihang']) && $item_sua['loaihang'] == $l) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($l) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col"><label>Nhóm hàng</label>
                        <select name="nhomhang" class="form-control">
                            <?php foreach ($ds_nhom_arr as $n): ?>
                                <option value="<?= htmlspecialchars($n) ?>"
                                    <?= (isset($item_sua['nhomhang']) && $item_sua['nhomhang'] == $n) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($n) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col"><label>Số lượng</label><input name="soluong" type="number" min="0"
                            class="form-control" value="<?= htmlspecialchars($item_sua['soluong'] ?? 1) ?>"></div>
                    <div class="col"><label>Đơn vị tính</label><input name="donvitinh" class="form-control"
                            placeholder="kg, gam, ml..." value="<?= htmlspecialchars($item_sua['donvitinh'] ?? '') ?>">
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col"><label>Tồn kho</label><input name="tonkho" type="number" min="0"
                            class="form-control" value="<?= htmlspecialchars($item_sua['tonkho'] ?? 0) ?>" required>
                    </div>
                    <div class="col"><label>Nhà cung cấp</label><input name="nhacungcap" class="form-control"
                            value="<?= htmlspecialchars($item_sua['nhacungcap'] ?? '') ?>"></div>
                    <div class="col"><label>Ghi chú</label><input name="ghichu" class="form-control"
                            value="<?= htmlspecialchars($item_sua['ghichu'] ?? '') ?>"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success"><?= $item_sua ? "Cập nhật" : "Lưu" ?></button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap 4 JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include('layout/footer.php'); ?>

<!-- Tự động mở modal nếu đang sửa -->
<?php if ($item_sua): ?>
    <script>
        $(document).ready(function() {
            $('#themModal').modal('show');
        });
    </script>
<?php endif; ?>