<?php
include('layout/header.php');
include('layout/sidebar.php');
include('../backend/db_connect.php');

$id = $_GET['id'] ?? 0;
if (!$id) {
    header("Location: hanghoa.php");
    exit;
}

// Lấy danh sách loại hàng & nhóm hàng
$ds_loai = $conn->query("SELECT ten FROM loaihang ORDER BY ten ASC");
$ds_nhom = $conn->query("SELECT ten FROM nhomhang ORDER BY ten ASC");

// Xử lý cập nhật
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    $sql = "UPDATE hanghoa SET 
                mahang='$mahang',
                tenhang='$tenhang',
                giavon='$giavon',
                loaihang='$loaihang',
                nhomhang='$nhomhang',
                soluong='$soluong',
                donvitinh='$donvitinh',
                tonkho='$tonkho',
                nhacungcap='$nhacungcap',
                ghichu='$ghichu'
            WHERE id=$id";
    $conn->query($sql);
    header("Location: hanghoa.php");
    exit;
}

// Lấy dữ liệu hiện tại
$row = $conn->query("SELECT * FROM hanghoa WHERE id = $id")->fetch_assoc();
?>

<div id="page-content-wrapper" class="p-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Cập nhật hàng hóa</h5>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-row mb-2">
                    <div class="col">
                        <label>Mã hàng hóa</label>
                        <input type="text" name="mahang" class="form-control"
                            value="<?= htmlspecialchars($row['mahang'] ?? '') ?>" required>
                    </div>
                    <div class="col">
                        <label>Tên hàng hóa</label>
                        <input type="text" name="tenhang" class="form-control"
                            value="<?= htmlspecialchars($row['tenhang'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="col">
                        <label>Giá nhập</label>
                        <input type="number" name="giavon" class="form-control" value="<?= $row['giavon'] ?? 0 ?>"
                            required>
                    </div>
                    <div class="col">
                        <label>Loại hàng</label>
                        <select name="loaihang" class="form-control">
                            <?php $ds_loai->data_seek(0);
                            while ($l = $ds_loai->fetch_assoc()): ?>
                            <option value="<?= $l['ten'] ?>"
                                <?= ($l['ten'] == ($row['loaihang'] ?? '')) ? 'selected' : '' ?>>
                                <?= $l['ten'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="col">
                        <label>Nhóm hàng</label>
                        <select name="nhomhang" class="form-control">
                            <?php $ds_nhom->data_seek(0);
                            while ($n = $ds_nhom->fetch_assoc()): ?>
                            <option value="<?= $n['ten'] ?>"
                                <?= ($n['ten'] == ($row['nhomhang'] ?? '')) ? 'selected' : '' ?>>
                                <?= $n['ten'] ?>
                            </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col">
                        <label>Số lượng</label>
                        <input type="number" name="soluong" class="form-control" value="<?= $row['soluong'] ?? 0 ?>">
                    </div>
                    <div class="col">
                        <label>Đơn vị tính</label>
                        <input type="text" name="donvitinh" class="form-control"
                            value="<?= htmlspecialchars($row['donvitinh'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="col">
                        <label>Tồn kho</label>
                        <input type="number" name="tonkho" class="form-control" value="<?= $row['tonkho'] ?? 0 ?>"
                            required>
                    </div>
                    <div class="col">
                        <label>Nhà cung cấp</label>
                        <input type="text" name="nhacungcap" class="form-control"
                            value="<?= htmlspecialchars($row['nhacungcap'] ?? '') ?>" placeholder="Tên hoặc link NCC">
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="col">
                        <label>Ghi chú</label>
                        <textarea name="ghichu" class="form-control" rows="2"
                            placeholder="Ghi chú thêm (link, mô tả...)"><?= htmlspecialchars($row['ghichu'] ?? '') ?></textarea>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="hanghoa.php" class="btn btn-secondary">Hủy</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('layout/footer.php'); ?>