<?php
include('../backend/db_connect.php');

$notifications = [];
$result = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC");
while ($row = $result->fetch_assoc()) {
    $notifications[] = $row;
}
?>

<?php if (!empty($notifications)): ?>
    <div class="alert-container" id="alertContainer">
        <?php foreach ($notifications as $notification): ?>
            <div class="custom-alert">
                <strong>Thông báo:</strong> <?= htmlspecialchars($notification['message']) ?>
                <button class="close-btn" onclick="this.parentElement.style.display='none';" aria-label="Close">&times;</button>
            </div>
        <?php endforeach; ?>
    </div>

    <script>
        // Nếu bạn muốn tự động ẩn thông báo sau 5 giây, có thể thêm đoạn này:
        window.addEventListener('DOMContentLoaded', () => {
            const alerts = document.querySelectorAll('.custom-alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    // Ẩn mượt bằng CSS transition
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.style.display = 'none', 500);
                }, 3000); // 3000ms = 3 giây
            });
        });
    </script>
<?php endif; ?>