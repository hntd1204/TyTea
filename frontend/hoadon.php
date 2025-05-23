<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$page = $_GET['page'] ?? 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Đếm tổng số hóa đơn để phân trang
$total = $conn->query("SELECT COUNT(*) as total FROM hoadon")->fetch_assoc()['total'];
$pages = ceil($total / $limit);

// Xử lý tạo hóa đơn
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ngaylap = $_POST['ngaylap'];
    $hanghoa_ids = $_POST['hanghoa_id'];
    $soluongs = $_POST['soluong'];

    $tongtien = 0;
    $ct = [];

    foreach ($hanghoa_ids as $index => $hanghoa_id) {
        $soluong = $soluongs[$index];
        if ($soluong <= 0 || !$hanghoa_id) continue;

        $res = $conn->query("SELECT giaban FROM hanghoa WHERE id = $hanghoa_id");
        $giaban = $res->fetch_assoc()['giaban'];
        $thanhtien = $giaban * $soluong;
        $tongtien += $thanhtien;

        $ct[] = [
            'hanghoa_id' => $hanghoa_id,
            'soluong' => $soluong,
            'thanhtien' => $thanhtien
        ];
    }

    // Thêm hóa đơn
    $conn->query("INSERT INTO hoadon (ngaylap, tongtien) VALUES ('$ngaylap', $tongtien)");
    $hoadon_id = $conn->insert_id;

    // Thêm chi tiết hóa đơn
    foreach ($ct as $item) {
        $conn->query("INSERT INTO chitiethoadon (hoadon_id, hanghoa_id, soluong, thanhtien)
                      VALUES ($hoadon_id, {$item['hanghoa_id']}, {$item['soluong']}, {$item['thanhtien']})");
    }

    header("Location: hoadon.php");
    exit;
}
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Tạo hóa đơn</h2>

    <!-- Form tạo hóa đơn -->
    <form method="POST">
        <div class="form-group">
            <label>Ngày lập</label>
            <input type="date" name="ngaylap" class="form-control" required>
        </div>

        <div id="danh-sach-hanghoa">
            <div class="form-row mb-2">
                <div class="col">
                    <select name="hanghoa_id[]" class="form-control" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        <?php
                        $result = $conn->query("SELECT * FROM hanghoa");
                        while ($hh = $result->fetch_assoc()) {
                            echo "<option value='{$hh['id']}'>{$hh['tenhang']} ({$hh['mahang']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col">
                    <input type="number" name="soluong[]" class="form-control" placeholder="Số lượng" min="1" required>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-danger btn-remove">X</button>
                </div>
            </div>
        </div>

        <button type="button" id="add-more" class="btn btn-secondary mb-3">+ Thêm sản phẩm</button>
        <br>
        <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Tạo hóa đơn</button>
    </form>

    <hr>
    <h3 class="mt-4">Danh sách hóa đơn</h3>
    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Ngày lập</th>
                <th>Tổng tiền</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $hoadons = $conn->query("SELECT * FROM hoadon ORDER BY id DESC LIMIT $offset, $limit");
            while ($hd = $hoadons->fetch_assoc()) {
                echo "<tr>
                        <td>{$hd['id']}</td>
                        <td>{$hd['ngaylap']}</td>
                        <td>" . number_format($hd['tongtien'], 0, ',', '.') . "đ</td>
                        <td>
                            <a href='chitiethoadon.php?id={$hd['id']}' class='btn btn-sm btn-info'>
                                <i class='fas fa-eye'></i> Xem
                            </a>
                            <a href='../backend/delete_hoadon.php?id={$hd['id']}' class='btn btn-sm btn-danger'
                               onclick=\"return confirm('Bạn có chắc chắn muốn xóa hóa đơn này không?');\">
                               <i class='fas fa-trash-alt'></i> Xóa
                            </a>
                        </td>
                    </tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- PHÂN TRANG -->
    <nav>
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>

<!-- JS thêm/xóa dòng sản phẩm -->
<script>
document.getElementById('add-more').addEventListener('click', function() {
    const container = document.getElementById('danh-sach-hanghoa');
    const row = container.children[0].cloneNode(true);
    row.querySelectorAll('input').forEach(input => input.value = '');
    container.appendChild(row);
});

document.getElementById('danh-sach-hanghoa').addEventListener('click', function(e) {
    if (e.target.classList.contains('btn-remove') && this.children.length > 1) {
        e.target.closest('.form-row').remove();
    }
});
</script>

<?php include('layout/footer.php'); ?>