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
            $conn->query("INSERT INTO phieunhapxuat (mahang, soluong, loai, nguoi_thuc_hien, ghichu) 
                          VALUES ('$mahang', $soluong, '$loai', '$nguoi', '$ghichu')");
            $message = "Thực hiện $loai kho thành công!";
        }
    }
}

// Lấy danh sách hàng hóa để chọn
$hanghoa = $conn->query("SELECT mahang, tenhang FROM hanghoa ORDER BY tenhang ASC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2>Quản lý Nhập / Xuất kho</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="col-md-4">
                <label>Mã hàng / Tên hàng</label>
                <select name="mahang" class="form-control" required>
                    <option value="">-- Chọn hàng hóa --</option>
                    <?php while ($row = $hanghoa->fetch_assoc()): ?>
                        <option value="<?= htmlspecialchars($row['mahang']) ?>">
                            <?= htmlspecialchars($row['mahang']) ?> - <?= htmlspecialchars($row['tenhang']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Số lượng</label>
                <input type="number" name="soluong" class="form-control" min="1" required>
            </div>
            <div class="col-md-2">
                <label>Loại</label>
                <select name="loai" class="form-control" required>
                    <option value="nhap">Nhập kho</option>
                    <option value="xuat">Xuất kho</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Người thực hiện</label>
                <input type="text" name="nguoi_thuc_hien" class="form-control" placeholder="Tên người" required>
            </div>
            <div class="col-md-2">
                <label>Ghi chú</label>
                <input type="text" name="ghichu" class="form-control" placeholder="Ghi chú (tuỳ chọn)">
            </div>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Xác nhận</button>
    </form>

    <h4>Lịch sử nhập xuất kho gần đây</h4>
    <table class="table table-bordered table-hover">
        <thead>
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

<?php include('layout/footer.php'); ?>