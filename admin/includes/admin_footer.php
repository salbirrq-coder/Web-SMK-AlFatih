        </div><!-- /.admin-content -->
    </main>
</div><!-- /.admin-layout -->

<script src="<?php echo SITE_URL; ?>assets/js/main.js"></script>
<script>
(function() {
    var sidebar = document.getElementById('sidebar');
    var toggle = document.getElementById('sbToggle');
    var overlay = document.getElementById('sbOverlay');
    if (toggle && sidebar) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            if (overlay) overlay.classList.toggle('active');
        });
    }
    if (overlay && sidebar) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
})();
</script>
</body>
</html>
