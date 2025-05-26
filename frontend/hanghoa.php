<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Xử lý thêm hàng hóa
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['mahang'])) {
    $mahang = $_POST['mahang'];
    $tenhang = $_POST['tenhang'];
    $giavon = $_POST['giavon'];
    $loaihang = $_POST['loaihang'];
    $nhomhang = $_POST['nhomhang'];
    $soluong = $_POST['soluong'];
    $donvitinh = $_POST['donvitinh'];
    $tonkho = $_POST['tonkho'];
    $nhacungcap = $_POST['nhacungcap'];
    $ghichu = $_POST['ghichu'];

    $sql = "INSERT INTO hanghoa 
        (mahang, tenhang, giavon, loaihang, nhomhang, soluong, donvitinh, tonkho, nhacungcap, ghichu)
        VALUES 
        ('$mahang', '$tenhang', '$giavon', '$loaihang', '$nhomhang', '$soluong', '$donvitinh', '$tonkho', '$nhacungcap', '$ghichu')";
    $conn->query($sql);
    header("Location: hanghoa.php");
    exit;
}

// Xử lý lọc
$search = $_GET['search'] ?? '';
$filter_nhom = $_GET['filter_nhom'] ?? '';
$filter_loai = $_GET['filter_loai'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
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

// Tổng số bản ghi
$countRes = $conn->query("SELECT COUNT(*) as total FROM hanghoa $where");
$totalRows = $countRes->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $limit);

// Lấy dữ liệu
$result = $conn->query("SELECT * FROM hanghoa $where ORDER BY id DESC LIMIT $limit OFFSET $offset");

// Dữ liệu lọc
$ds_loai = $conn->query("SELECT ten FROM loaihang ORDER BY ten ASC");
$ds_nhom = $conn->query("SELECT ten FROM nhomhang ORDER BY ten ASC");

// Tính tổng tiền nhập
$sumRes = $conn->query("SELECT SUM(giavon * soluong) AS tongtien FROM hanghoa $where");
$tongtien = $sumRes->fetch_assoc()['tongtien'] ?? 0;
?>

<div id="page-content-wrapper" class="p-4">
    <div class="d-flex justify-content-between mb-3 align-items-center">
        <h2>Danh sách hàng hóa</h2>
        <button class="btn btn-success" data-toggle="modal" data-target="#themModal">
            <i class="fas fa-plus"></i> Thêm mới
        </button>
    </div>

    <!-- Lọc -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm theo mã, tên, NCC..."
            value="<?= htmlspecialchars($search) ?>">
        <select name="filter_nhom" class="form-control mr-2">
            <option value="">-- Nhóm hàng --</option>
            <?php
            $ds_nhom_filter = $conn->query("SELECT DISTINCT nhomhang FROM hanghoa ORDER BY nhomhang ASC");
            while ($nh = $ds_nhom_filter->fetch_assoc()) {
                $selected = ($filter_nhom == $nh['nhomhang']) ? 'selected' : '';
                echo "<option value='{$nh['nhomhang']}' $selected>{$nh['nhomhang']}</option>";
            }
            ?>
        </select>
        <select name="filter_loai" class="form-control mr-2">
            <option value="">-- Loại hàng --</option>
            <?php
            $ds_loai_filter = $conn->query("SELECT DISTINCT loaihang FROM hanghoa ORDER BY loaihang ASC");
            while ($lh = $ds_loai_filter->fetch_assoc()) {
                $selected = ($filter_loai == $lh['loaihang']) ? 'selected' : '';
                echo "<option value='{$lh['loaihang']}' $selected>{$lh['loaihang']}</option>";
            }
            ?>
        </select>
        <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Lọc</button>
    </form>

    <!-- Tổng tiền nhập -->
    <div class="text-right mb-2 font-weight-bold">
        Tổng tiền nhập: <span class="text-danger"><?= number_format($tongtien, 0, ',', '.') ?> đ</span>
    </div>

    <!-- Bảng -->
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
                    <td><?= number_format($row['giavon'], 0, ',', '.') ?>đ</td>
                    <td><?= htmlspecialchars($row['loaihang']) ?></td>
                    <td><?= htmlspecialchars($row['nhomhang']) ?></td>
                    <td><?= $row['soluong'] ?></td>
                    <td><?= htmlspecialchars($row['donvitinh']) ?></td>
                    <td><?= $row['tonkho'] ?></td>
                    <td><?= htmlspecialchars($row['nhacungcap']) ?></td>
                    <td><?= htmlspecialchars($row['ghichu'] ?? '') ?></td>
                    <td>
                        <a href="edit_hanghoa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                        <a href="../backend/delete_hanghoa.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xác nhận xóa?')">Xóa</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= ($i == $page ? 'active' : '') ?>">
                <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- Modal thêm mới -->
<div class="modal fade" id="themModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <form method="POST" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm hàng hóa</h5>
                <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-row mb-2">
                    <div class="col"><label>Mã hàng hóa</label><input name="mahang" class="form-control" required></div>
                    <div class="col"><label>Tên hàng hóa</label><input name="tenhang" class="form-control" required>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col"><label>Giá nhập</label><input name="giavon" type="number" class="form-control"
                            required></div>
                    <div class="col"><label>Loại hàng</label>
                        <select name="loaihang" class="form-control">
                            <?php while ($l = $ds_loai->fetch_assoc()) echo "<option value='{$l['ten']}'>{$l['ten']}</option>"; ?>
                        </select>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="col"><label>Nhóm hàng</label>
                        <select name="nhomhang" class="form-control">
                            <?php while ($n = $ds_nhom->fetch_assoc()) echo "<option value='{$n['ten']}'>{$n['ten']}</option>"; ?>
                        </select>
                    </div>
                    <div class="col"><label>Số lượng</label><input name="soluong" type="number" class="form-control"
                            value="1"></div>
                    <div class="col"><label>Đơn vị tính</label><input name="donvitinh" class="form-control"
                            placeholder="kg, gam, ml..."></div>
                </div>
                <div class="form-row mb-2">
                    <div class="col"><label>Tồn kho</label><input name="tonkho" type="number" class="form-control"
                            required></div>
                    <div class="col"><label>Nhà cung cấp</label><input name="nhacungcap" class="form-control"></div>
                    <div class="col"><label>Ghi chú</label><input name="ghichu" class="form-control"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success">Lưu</button>
                <button class="btn btn-secondary" data-dismiss="modal">Hủy</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<?php include('layout/footer.php'); ?>