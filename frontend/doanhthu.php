<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
$from = $_GET['from'] ?? '';
$to = $_GET['to'] ?? '';

$condition = "";
if ($from && $to) {
    $condition = "WHERE ngaylap BETWEEN '$from' AND '$to'";
} elseif ($from) {
    $condition = "WHERE ngaylap >= '$from'";
} elseif ($to) {
    $condition = "WHERE ngaylap <= '$to'";
}

// Tổng kết doanh thu
$tong = $conn->query("SELECT COUNT(*) as sohd, SUM(tongtien) as tongtien FROM hoadon $condition")->fetch_assoc();
$hoadons = $conn->query("SELECT * FROM hoadon $condition ORDER BY ngaylap DESC");

// Dữ liệu biểu đồ doanh thu
$dataChart = $conn->query("
    SELECT ngaylap, SUM(tongtien) as doanhthu
    FROM hoadon $condition
    GROUP BY ngaylap
    ORDER BY ngaylap ASC
");

$labels = [];
$values = [];
while ($r = $dataChart->fetch_assoc()) {
    $labels[] = $r['ngaylap'];
    $values[] = $r['doanhthu'];
}

// Top sản phẩm bán chạy
$sqlTop = "
    SELECT hh.mahang, hh.tenhang, SUM(ct.soluong) as tongban
    FROM chitiethoadon ct
    JOIN hoadon hd ON ct.hoadon_id = hd.id
    JOIN hanghoa hh ON ct.hanghoa_id = hh.id
    $condition
    GROUP BY ct.hanghoa_id
    ORDER BY tongban DESC
    LIMIT 5
";
$topProducts = $conn->query($sqlTop);
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Báo cáo doanh thu</h2>

    <!-- Bộ lọc ngày -->
    <form method="GET" class="form-inline mb-4">
        <label class="mr-2 font-weight-bold">Từ ngày:</label>
        <input type="date" name="from" class="form-control mr-3" value="<?= $from ?>">

        <label class="mr-2 font-weight-bold">Đến ngày:</label>
        <input type="date" name="to" class="form-control mr-3" value="<?= $to ?>">

        <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Lọc</button>
    </form>

    <!-- Tổng kết -->
    <div class="alert alert-info">
        <strong>Tổng số hóa đơn:</strong> <?= $tong['sohd'] ?? 0 ?> |
        <strong>Tổng doanh thu:</strong> <?= number_format($tong['tongtien'] ?? 0, 0, ',', '.') ?>đ
    </div>

    <!-- Danh sách hóa đơn -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Ngày lập</th>
                <th>Tổng tiền</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $hoadons->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['ngaylap'] ?></td>
                <td><?= number_format($row['tongtien'], 0, ',', '.') ?>đ</td>
                <td>
                    <a href="chitiethoadon.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                        <i class="fas fa-eye"></i> Xem
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Biểu đồ doanh thu -->
    <h4 class="mt-5 mb-3">Biểu đồ doanh thu theo ngày</h4>
    <canvas id="chartDoanhThu" height="100"></canvas>

    <!-- Top sản phẩm bán chạy -->
    <h4 class="mt-5 mb-3">Top 5 sản phẩm bán chạy</h4>
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Mã hàng</th>
                <th>Tên hàng</th>
                <th>Số lượng bán</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $topProducts->fetch_assoc()): ?>
            <tr>
                <td><?= $row['mahang'] ?></td>
                <td><?= $row['tenhang'] ?></td>
                <td><?= $row['tongban'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Vẽ biểu đồ -->
<script>
const ctx = document.getElementById('chartDoanhThu').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
            label: 'Doanh thu (VNĐ)',
            data: <?= json_encode($values) ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
        }]
    },
    options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>

<?php include('layout/footer.php'); ?>