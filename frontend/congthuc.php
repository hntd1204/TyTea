<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$mon_id = $_GET['mon_id'] ?? 0;
$mon = $conn->query("SELECT * FROM monban WHERE id = $mon_id")->fetch_assoc();

if (!$mon) {
    echo "<div class='p-4'>Món không tồn tại.</div>";
    exit;
}

// Sửa
$edit_id = $_GET['edit'] ?? null;
$nguyenlieu_sua = null;
if ($edit_id) {
    $nguyenlieu_sua = $conn->query("SELECT * FROM congthuc_mon WHERE id = $edit_id")->fetch_assoc();
}

// Thêm hoặc cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['thanh_phan'])) {
    $tp = $conn->real_escape_string($_POST['thanh_phan']);
    $sl = floatval($_POST['so_luong']);
    $dv = $conn->real_escape_string($_POST['don_vi']);
    $ghichu = $conn->real_escape_string($_POST['ghi_chu']);

    if (isset($_POST['id_update']) && is_numeric($_POST['id_update'])) {
        $id_up = (int)$_POST['id_update'];
        $conn->query("UPDATE congthuc_mon SET 
                        thanh_phan='$tp', 
                        so_luong=$sl, 
                        don_vi='$dv', 
                        ghi_chu='$ghichu' 
                      WHERE id=$id_up");
    } else {
        $conn->query("INSERT INTO congthuc_mon (mon_id, thanh_phan, so_luong, don_vi, ghi_chu)
                      VALUES ($mon_id, '$tp', $sl, '$dv', '$ghichu')");
    }

    header("Location: congthuc.php?mon_id=$mon_id");
    exit;
}

// Xoá
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $conn->query("DELETE FROM congthuc_mon WHERE id = $id");
    header("Location: congthuc.php?mon_id=$mon_id");
    exit;
}

$ct = $conn->query("SELECT * FROM congthuc_mon WHERE mon_id = $mon_id ORDER BY id ASC");
?>

<div id="page-content-wrapper" class="p-4">
    <h3 class="mb-4">🧪 Công thức cho món: <span class="text-primary"><?= htmlspecialchars($mon['ten']) ?></span></h3>

    <!-- Form thêm/sửa nguyên liệu -->
    <form method="POST" class="mb-4">
        <div class="form-row mb-2">
            <div class="col">
                <input name="thanh_phan" class="form-control" placeholder="Nguyên liệu" required
                    value="<?= htmlspecialchars($nguyenlieu_sua['thanh_phan'] ?? '') ?>">
            </div>
            <div class="col">
                <input name="so_luong" type="number" step="0.01" class="form-control" placeholder="Số lượng" required
                    value="<?= htmlspecialchars($nguyenlieu_sua['so_luong'] ?? '') ?>">
            </div>
            <div class="col">
                <input name="don_vi" class="form-control" placeholder="Đơn vị (ml, gam...)"
                    value="<?= htmlspecialchars($nguyenlieu_sua['don_vi'] ?? '') ?>">
            </div>
            <div class="col">
                <input name="ghi_chu" class="form-control" placeholder="Ghi chú (tuỳ chọn)"
                    value="<?= htmlspecialchars($nguyenlieu_sua['ghi_chu'] ?? '') ?>">
            </div>
            <div class="col-auto">
                <?php if ($nguyenlieu_sua): ?>
                    <input type="hidden" name="id_update" value="<?= $nguyenlieu_sua['id'] ?>">
                    <button class="btn btn-warning"><i class="fas fa-save"></i> Cập nhật</button>
                    <a href="congthuc.php?mon_id=<?= $mon_id ?>" class="btn btn-secondary ml-2">Hủy sửa</a>
                <?php else: ?>
                    <button class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
                <?php endif; ?>
            </div>
        </div>
    </form>

    <!-- Bảng công thức -->
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Nguyên liệu</th>
                <th>Số lượng</th>
                <th>Đơn vị</th>
                <th>Ghi chú</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php $i = 1;
            while ($row = $ct->fetch_assoc()): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= htmlspecialchars($row['thanh_phan']) ?></td>
                    <td><?= $row['so_luong'] ?></td>
                    <td><?= htmlspecialchars($row['don_vi']) ?></td>
                    <td><?= htmlspecialchars($row['ghi_chu']) ?></td>
                    <td>
                        <a href="?mon_id=<?= $mon_id ?>&edit=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Sửa</a>
                        <a href="?mon_id=<?= $mon_id ?>&delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Xoá nguyên liệu này?')">Xoá</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="monban.php" class="btn btn-secondary mt-3">← Quay lại danh sách món</a>
</div>

<?php include('layout/footer.php'); ?>