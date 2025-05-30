<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';

// Xử lý thêm nhập xuất kho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahang = $conn->real_escape_string($_POST['mahang']);
    $soluong = (int)$_POST['soluong'];
    $loai = $_POST['loai']; // 'nhap' hoặc 'xuat'
    $nguoi = $conn->real_escape_string($_POST['nguoi_thuc_hien']);
    $ghichu = $conn->real_escape_string($_POST['ghichu']);

    // Lấy số lượng và tồn kho hiện tại
    $hh = $conn->query("SELECT soluong, tonkho FROM hanghoa WHERE mahang = '$mahang'");
    if (!$hh) {
        die("Lỗi truy vấn tồn kho: " . $conn->error);
    }
    $hh_data = $hh->fetch_assoc();
    if (!$hh_data) {
        $message = "Mã hàng không tồn tại!";
    } else {
        $soluong_hh = (int)$hh_data['soluong'];
        $tonkho_hh = (int)$hh_data['tonkho'];

        if ($loai === 'nhap') {
            // Nhập hàng: tăng số lượng và tồn kho
            $soluong_moi = $soluong_hh + $soluong;
            $tonkho_moi = $tonkho_hh + $soluong;

            $update = $conn->query("UPDATE hanghoa SET soluong = $soluong_moi, tonkho = $tonkho_moi WHERE mahang = '$mahang'");
        } else {
            // Xuất hàng: chỉ giảm tồn kho
            if ($soluong > $tonkho_hh) {
                $message = "Số lượng xuất lớn hơn tồn kho hiện có ($tonkho_hh). Không thể xuất.";
            } else {
                $tonkho_moi = $tonkho_hh - $soluong;
                $update = $conn->query("UPDATE hanghoa SET tonkho = $tonkho_moi WHERE mahang = '$mahang'");
            }
        }

        if (!$message) {
            if (!$update) {
                die("Lỗi cập nhật tồn kho: " . $conn->error);
            }
            $insert = $conn->query("INSERT INTO phieunhapxuat (mahang, soluong, loai, nguoi_thuc_hien, ghichu, ngaytao) 
                VALUES ('$mahang', $soluong, '$loai', '$nguoi', '$ghichu', NOW())");
            if (!$insert) {
                die("Lỗi lưu lịch sử nhập xuất: " . $conn->error);
            }
            $message = "Thực hiện " . ($loai === 'nhap' ? "nhập" : "xuất") . " kho thành công!";
        }
    }
}

// Lọc & phân trang
$filter_mahang = $_GET['mahang'] ?? '';
$ngaybatdau = $_GET['ngaybatdau'] ?? '';
$ngayketthuc = $_GET['ngayketthuc'] ?? '';

$conds = [];
if ($filter_mahang !== '') {
    $mahang_safe = $conn->real_escape_string($filter_mahang);
    $conds[] = "p.mahang = '$mahang_safe'";
}
if ($ngaybatdau !== '') {
    $nbd_safe = $conn->real_escape_string($ngaybatdau);
    $conds[] = "p.ngaytao >= '$nbd_safe 00:00:00'";
}
if ($ngayketthuc !== '') {
    $nkt_safe = $conn->real_escape_string($ngayketthuc);
    $conds[] = "p.ngaytao <= '$nkt_safe 23:59:59'";
}
$where = count($conds) ? 'WHERE ' . implode(' AND ', $conds) : '';

$countRes = $conn->query("SELECT COUNT(*) AS total FROM phieunhapxuat p $where");
$totalRows = $countRes->fetch_assoc()['total'] ?? 0;

$limit = 15;
$page = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
$totalPages = ceil($totalRows / $limit);

// Lấy danh sách hàng hóa để select
$hanghoa_res = $conn->query("SELECT mahang, tenhang FROM hanghoa ORDER BY tenhang ASC");

// Lấy dữ liệu lịch sử theo filter và phân trang
$sql_history = "
    SELECT p.*, h.tenhang
    FROM phieunhapxuat p
    LEFT JOIN hanghoa h ON p.mahang = h.mahang
    $where
    ORDER BY p.ngaytao DESC
    LIMIT $limit OFFSET $offset
";
$history = $conn->query($sql_history);
?>

<div id="page-content-wrapper" class="p-4">
    <h2>Quản lý Nhập Xuất Kho</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Form lọc -->
    <form method="GET" class="form-inline mb-3">
        <select name="mahang" class="form-control mr-2" style="min-width: 200px;">
            <option value="">-- Tất cả hàng hóa --</option>
            <?php while ($row = $hanghoa_res->fetch_assoc()): ?>
                <option value="<?= htmlspecialchars($row['mahang']) ?>"
                    <?= ($filter_mahang == $row['mahang']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['mahang']) ?> - <?= htmlspecialchars($row['tenhang']) ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label class="mr-2">Từ ngày:</label>
        <input type="date" name="ngaybatdau" value="<?= htmlspecialchars($ngaybatdau) ?>" class="form-control mr-2">

        <label class="mr-2">Đến ngày:</label>
        <input type="date" name="ngayketthuc" value="<?= htmlspecialchars($ngayketthuc) ?>" class="form-control mr-2">

        <button class="btn btn-primary mr-2">Lọc</button>
        <a href="export_excel_nhapxuat.php?<?= http_build_query($_GET) ?>" class="btn btn-success" target="_blank">Xuất
            Excel</a>
    </form>

    <!-- Lịch sử nhập xuất kho -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-sm">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Mã hàng</th>
                    <th>Tên hàng</th>
                    <th>Số lượng</th>
                    <th>Loại</th>
                    <th>Người thực hiện</th>
                    <th>Ghi chú</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($totalRows == 0): ?>
                    <tr>
                        <td colspan="8" class="text-center">Không có dữ liệu</td>
                    </tr>
                    <?php else:
                    $i = $offset + 1;
                    while ($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($row['mahang']) ?></td>
                            <td><?= htmlspecialchars($row['tenhang'] ?? '') ?></td>
                            <td><?= $row['soluong'] ?></td>
                            <td><?= ucfirst($row['loai']) ?></td>
                            <td><?= htmlspecialchars($row['nguoi_thuc_hien']) ?></td>
                            <td><?= htmlspecialchars($row['ghichu']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['ngaytao'])) ?></td>
                        </tr>
                <?php endwhile;
                endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    <?php if ($totalPages > 1): ?>
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
    <?php endif; ?>

    <!-- Form nhập xuất kho -->
    <h3>Nhập/Xuất Kho</h3>
    <form method="POST" class="mt-4">
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <label for="mahang">Mã hàng / Tên hàng</label>
                <select id="mahang" name="mahang" class="form-control select2" style="width: 100%;" required>
                    <option value="">-- Chọn hàng hóa --</option>
                    <?php
                    // Lấy lại danh sách cho select (nếu cần)
                    $hanghoa2 = $conn->query("SELECT mahang, tenhang FROM hanghoa ORDER BY tenhang ASC");
                    while ($row = $hanghoa2->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['mahang']) ?>">
                            <?= htmlspecialchars($row['mahang']) ?> - <?= htmlspecialchars($row['tenhang']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="soluong">Số lượng</label>
                <input type="number" id="soluong" name="soluong" class="form-control" min="1" required>
            </div>
            <div class="col-md-2 mb-3">
                <label for="loai">Loại</label>
                <select id="loai" name="loai" class="form-control" required>
                    <option value="nhap">Nhập kho</option>
                    <option value="xuat">Xuất kho</option>
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label for="nguoi_thuc_hien">Người thực hiện</label>
                <input type="text" id="nguoi_thuc_hien" name="nguoi_thuc_hien" class="form-control"
                    placeholder="Tên người" required>
            </div>
            <div class="col-md-12 mb-3">
                <label for="ghichu">Ghi chú</label>
                <input type="text" id="ghichu" name="ghichu" class="form-control" placeholder="Ghi chú (tuỳ chọn)">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Xác nhận</button>
    </form>
</div>

<!-- jQuery + Select2 -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#mahang').select2({
            placeholder: "-- Chọn hàng hóa --",
            allowClear: true
        });
    });
</script>

<?php include('layout/footer.php'); ?>