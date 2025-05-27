<div class="bg-primary border-right shadow" id="sidebar-wrapper">
    <div class="text-center py-3">
        <img src="../img/logo.jpg" alt="TyTea Logo" style="max-width: 120px; height: auto;">
    </div>
    <div class="sidebar-heading text-white font-weight-bold text-center py-2" style="font-size: 1.2rem;">
        TyTea - Matcha and Tea
    </div>
    <nav class="list-group list-group-flush">
        <?php
        $menus = [
            ['link' => 'index.php', 'icon' => 'fas fa-chart-pie', 'label' => 'Tổng quan'],
            ['link' => 'hanghoa.php', 'icon' => 'fas fa-box', 'label' => 'Hàng hóa'],
            ['link' => 'nhapkho_xuatkho.php', 'icon' => 'fas fa-warehouse', 'label' => 'Nhập / Xuất kho'],
            ['link' => 'monban.php', 'icon' => 'fas fa-utensils', 'label' => 'Món bán'],
            ['link' => 'nhacungcap.php', 'icon' => 'fas fa-truck', 'label' => 'Nhà cung cấp'],
            ['link' => 'nhomhang.php', 'icon' => 'fas fa-layer-group', 'label' => 'Nhóm hàng'],
            ['link' => 'loaihang.php', 'icon' => 'fas fa-tags', 'label' => 'Loại hàng'],
            ['link' => 'loaithucdon.php', 'icon' => 'fas fa-list-alt', 'label' => 'Loại thực đơn'],
            ['link' => 'upload_hoadon.php', 'icon' => 'fas fa-upload', 'label' => 'Tải lên hóa đơn'],
            ['link' => 'danhsach_hoadon.php', 'icon' => 'fas fa-image', 'label' => 'Hóa đơn đã lưu'],

            ['link' => 'logout.php', 'icon' => 'fas fa-sign-out-alt', 'label' => 'Đăng xuất'],
        ];

        foreach ($menus as $item) {
            echo "<a href=\"{$item['link']}\" class=\"list-group-item list-group-item-action bg-primary text-white\">";
            echo "<i class=\"{$item['icon']} mr-2\"></i> {$item['label']}</a>";
        }
        ?>
    </nav>
</div>