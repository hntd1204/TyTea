<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

$message = '';

// Xử lý form nhập/xuất kho
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahang = $conn->real_escape_string($_POST['mahang']);
    $soluong = (int)$_POST['soluong'];
    $loai = $_POST['loai']; // 'nhap' hoặc 'xuat'
    $nguoi = $conn->real_escape_string($_POST['nguoi_thuc_hien']);
    $ghichu = $conn->real_escape_string($_POST['ghichu']);

    // Lấy tồn kho hiện tại
    $hh = $conn->query("SELECT tonkho FROM hanghoa WHERE mahang = '$mahang'")->fetch_assoc();
    if (!$hh) {
        $message = "Mã hàng không tồn tại!";
    } else {
        $tonkho_hien_tai = (int)$hh['tonkho'];

        if ($loai === 'nhap') {
            $tonkho_moi = $tonkho_hien_tai + $soluong;
        } else {
            if ($soluong > $tonkho_hien_tai) {
                $message = "Số lượng xuất lớn hơn tồn kho hiện có ($tonkho_hien_tai). Không thể xuất.";
            } else {
                $tonkho_moi = $tonkho_hien_tai - $soluong;
            }
        }

        if (!$message) {
            // Cập nhật tồn kho
            $conn->query("UPDATE hanghoa SET tonkho = $tonkho_moi WHERE mahang = '$mahang'");
            // Lưu lịch sử
            $conn->query("INSERT INTO phieunhapxuat (mahang, soluong, loai, nguoi_thuc_hien, ghichu, ngaytao) 
                          VALUES ('$mahang', $soluong, '$loai', '$nguoi', '$ghichu', NOW())");
            $message = "Thực hiện " . ($loai === 'nhap' ? "nhập" : "xuất") . " kho thành công!";
        }
    }
}

// Lấy toàn bộ danh sách hàng hóa để load lên select
$hanghoa = $conn->query("SELECT mahang, tenhang FROM hanghoa ORDER BY tenhang ASC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2>Quản lý Nhập / Xuất kho</h2>

    <?php if ($message): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="col-md-6 mb-3">
                <label for="mahang">Mã hàng / Tên hàng</label>
                <select id="mahang" name="mahang" class="form-control select2" style="width: 100%;" required>
                    <option value="">-- Chọn hàng hóa --</option>
                    <?php while ($row = $hanghoa->fetch_assoc()): ?>
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

        <button type="submit" class="btn btn-primary mt-2">Xác nhận</button>
    </form>

    <h4>Lịch sử nhập xuất kho gần đây</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Mã hàng</th>
                    <th>Số lượng</th>
                    <th>Loại</th>
                    <th>Người thực hiện</th>
                    <th>Ghi chú</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $history = $conn->query("SELECT * FROM phieunhapxuat ORDER BY ngaytao DESC LIMIT 20");
                $i = 1;
                while ($row = $history->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['mahang']) ?></td>
                    <td><?= $row['soluong'] ?></td>
                    <td><?= ucfirst($row['loai']) ?></td>
                    <td><?= htmlspecialchars($row['nguoi_thuc_hien']) ?></td>
                    <td><?= htmlspecialchars($row['ghichu']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['ngaytao'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Thêm jQuery + Select2 -->
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