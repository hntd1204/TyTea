<?php include('layout/header.php'); ?>
<?php include('layout/sidebar.php'); ?>
<?php include('../backend/db_connect.php'); ?>

<?php
// Xử lý thêm khách hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ten'])) {
    $ten = $_POST['ten'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $diachi = $_POST['diachi'];

    $sql = "INSERT INTO khachhang (ten, sdt, email, diachi)
            VALUES ('$ten', '$sdt', '$email', '$diachi')";
    $conn->query($sql);
    header("Location: khachhang.php");
    exit;
}

// Tìm kiếm
$search = $_GET['search'] ?? '';
$search_sql = "";
if (!empty($search)) {
    $search = $conn->real_escape_string($search);
    $search_sql = "WHERE ten LIKE '%$search%' OR sdt LIKE '%$search%' OR email LIKE '%$search%'";
}

// Lấy danh sách
$result = $conn->query("SELECT * FROM khachhang $search_sql ORDER BY id DESC");
?>

<div id="page-content-wrapper" class="p-4">
    <h2 class="mb-4">Quản lý khách hàng</h2>

    <!-- Form tìm kiếm -->
    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Tìm tên, sđt, email..."
            value="<?= htmlspecialchars($search) ?>">
        <button type="submit" class="btn btn-outline-primary">Tìm</button>
    </form>

    <!-- Form thêm khách hàng -->
    <form method="POST" class="mb-4">
        <div class="form-row">
            <div class="col"><input type="text" name="ten" class="form-control" placeholder="Họ tên" required></div>
            <div class="col"><input type="text" name="sdt" class="form-control" placeholder="Số điện thoại"></div>
            <div class="col"><input type="email" name="email" class="form-control" placeholder="Email"></div>
            <div class="col"><input type="text" name="diachi" class="form-control" placeholder="Địa chỉ"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Thêm</button>
            </div>
        </div>
    </form>

    <!-- Danh sách khách hàng -->
    <table class="table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>SĐT</th>
                <th>Email</th>
                <th>Địa chỉ</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['ten']) ?></td>
                <td><?= htmlspecialchars($row['sdt']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['diachi']) ?></td>
                <td>
                    <a href="edit_khachhang.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <a href="../backend/delete_khachhang.php?id=<?= $row['id'] ?>"
                        onclick="return confirm('Bạn có chắc muốn xóa khách hàng này?');" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash-alt"></i> Xóa
                    </a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include('layout/footer.php'); ?>