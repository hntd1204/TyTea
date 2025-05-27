<script>
    document.getElementById('sidebarToggle').addEventListener('click', function() {
        document.getElementById('sidebar-wrapper').classList.toggle('active');
        document.getElementById('overlay').classList.toggle('active');
    });
    document.getElementById('overlay').addEventListener('click', function() {
        this.classList.remove('active');
        document.getElementById('sidebar-wrapper').classList.remove('active');
    });
</script>