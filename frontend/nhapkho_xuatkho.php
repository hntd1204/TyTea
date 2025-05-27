<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$message = '';

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

// Lấy danh sách hàng hóa
$hanghoa = $conn->query("SELECT mahang, tenhang FROM hanghoa ORDER BY tenhang ASC");
if (!$hanghoa) {
    die("Lỗi truy vấn danh sách hàng hóa: " . $conn->error);
}

// Lấy lịch sử nhập xuất kho (join tên hàng)
$history = $conn->query("
    SELECT p.*, h.tenhang 
    FROM phieunhapxuat p
    LEFT JOIN hanghoa h ON p.mahang = h.mahang
    ORDER BY p.ngaytao DESC
    LIMIT 20
");
if (!$history) {
    die("Lỗi truy vấn lịch sử nhập xuất: " . $conn->error);
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2>Quản lý Nhập Xuất Kho</h2>

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

        <button type="submit" class="btn btn-primary">Xác nhận</button>
    </form>

    <h4>Lịch sử nhập xuất kho gần đây</h4>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
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
                $i = 1;
                while ($row = $history->fetch_assoc()):
                ?>
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
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Thư viện jQuery + Select2 -->
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