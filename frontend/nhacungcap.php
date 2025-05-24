<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
$search = $_GET['search'] ?? '';
$cond = '';

if (!empty($search)) {
    $s = $conn->real_escape_string($search);
    $cond = "AND nhacungcap LIKE '%$s%'";
}

$result = $conn->query("
    SELECT nhacungcap, COUNT(*) AS sohang
    FROM hanghoa
    WHERE nhacungcap IS NOT NULL AND nhacungcap != ''
    $cond
    GROUP BY nhacungcap
    ORDER BY nhacungcap ASC
");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Nhà cung cấp</h2>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm nhà cung cấp..."
            value="<?= htmlspecialchars($search) ?>">
        <button class="btn btn-outline-primary"><i class="fas fa-search"></i> Tìm</button>
    </form>

    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Tên nhà cung cấp</th>
                <th>Số lượng sản phẩm</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <a href="ncc_hanghoa.php?ncc=<?= urlencode($row['nhacungcap']) ?>">
                            <?= htmlspecialchars($row['nhacungcap']) ?>
                        </a>
                    </td>
                    <td><?= $row['sohang'] ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>