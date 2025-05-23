<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
$search = $_GET['search'] ?? '';
$cond = '';

if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $cond = "WHERE nhacungcap LIKE '%$s%'";
}

$result = $conn->query("
    SELECT nhacungcap, COUNT(*) as sohang
    FROM hanghoa
    WHERE nhacungcap IS NOT NULL AND nhacungcap != ''
    " . ($cond ? "AND nhacungcap LIKE '%$search%'" : "") . "
    GROUP BY nhacungcap
    ORDER BY nhacungcap ASC
");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Nhà cung cấp</h2>

    <!-- Tìm kiếm -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm nhà cung cấp..."
            value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Tìm</button>
    </form>

    <!-- Danh sách NCC -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Tên nhà cung cấp</th>
                <th>Số lượng hàng hóa</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($ncc = $result->fetch_assoc()): ?>
            <tr>
                <td>
                    <a href="ncc_hanghoa.php?ncc=<?= urlencode($ncc['nhacungcap']) ?>">
                        <?= htmlspecialchars($ncc['nhacungcap']) ?>
                    </a>
                </td>
                <td><?= $ncc['sohang'] ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>