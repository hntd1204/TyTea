<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php');

// Thêm loại
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ten'])) {
    $ten = trim($_POST['ten']);
    if ($ten != '') {
        $conn->query("INSERT INTO loaithucdon (ten) VALUES ('$ten')");
        header("Location: loaithucdon.php");
        exit;
    }
}

// Xoá loại
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM loaithucdon WHERE id = $id");
    header("Location: loaithucdon.php");
    exit;
}

// Lấy danh sách
$result = $conn->query("SELECT * FROM loaithucdon ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Loại thực đơn</h2>

    <form method="POST" class="form-inline mb-3">
        <input type="text" name="ten" class="form-control mr-2" placeholder="Tên loại thực đơn" required>
        <button class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
    </form>

    <table class="table table-bordered">
        <thead class="thead-light">
            <tr>
                <th>#</th>
                <th>Tên loại</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['id'] ?></td>
                    <td><?= htmlspecialchars($row['ten']) ?></td>
                    <td>
                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                            onclick="return confirm('Xoá loại thực đơn này?')">Xoá</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>