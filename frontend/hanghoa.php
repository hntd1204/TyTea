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

    $sql = "INSERT INTO hanghoa 
        (mahang, tenhang, giavon, loaihang, nhomhang, soluong, donvitinh, tonkho, nhacungcap)
        VALUES 
        ('$mahang', '$tenhang', '$giavon', '$loaihang', '$nhomhang', '$soluong', '$donvitinh', '$tonkho', '$nhacungcap')";
    $conn->query($sql);
    header("Location: hanghoa.php");
    exit;
}
$search = $_GET['search'] ?? '';
$cond = '';

if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $cond = "WHERE mahang LIKE '%$s%' OR tenhang LIKE '%$s%' OR nhacungcap LIKE '%$s%'";
}

$result = $conn->query("SELECT * FROM hanghoa $cond ORDER BY id DESC");


// Lấy dữ liệu danh sách
$result = $conn->query("SELECT * FROM hanghoa ORDER BY id DESC");
$ds_loai = $conn->query("SELECT ten FROM loaihang ORDER BY ten ASC");
$ds_nhom = $conn->query("SELECT ten FROM nhomhang ORDER BY ten ASC");
?>

<div id="page-content-wrapper" class="p-4">
    <div class="d-flex justify-content-between mb-4 align-items-center">
        <h2>Danh sách hàng hóa</h2>
        <button class="btn btn-success" data-toggle="modal" data-target="#themModal">
            <i class="fas fa-plus"></i> Thêm mới
        </button>
    </div>
    <!-- Form tìm kiếm -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm theo mã, tên hoặc nhà cung cấp..."
            value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Tìm</button>
    </form>

    <!-- Modal Thêm -->
    <div class="modal fade" id="themModal" tabindex="-1" role="dialog" aria-labelledby="themModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <form method="POST" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Thêm hàng hóa</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-row mb-2">
                        <div class="col"><label>Mã hàng hóa</label><input name="mahang" class="form-control" required>
                        </div>
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
                        <div class="col"><label>Nhà cung cấp</label><input name="nhacungcap" class="form-control"
                                placeholder="Nhập tên hoặc link"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Lưu</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng dữ liệu -->
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>Mã hàng</th>
                    <th>Tên hàng</th>
                    <th>Giá nhập</th>
                    <th>Loại hàng</th>
                    <th>Nhóm hàng</th>
                    <th>Số lượng</th>
                    <th>Đơn vị</th>
                    <th>Tồn kho</th>
                    <th>Nhà cung cấp</th>
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
</div>

<!-- Bootstrap & jQuery nếu chưa có -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>

<?php include('layout/footer.php'); ?>