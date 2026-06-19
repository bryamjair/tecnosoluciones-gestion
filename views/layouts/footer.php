<?php if (isset($_SESSION['user_id'])): ?>
    </div>
<?php endif; ?>

<script>
    var sidebarLinks = document.querySelectorAll('.nav-item');
    for (var i = 0; i < sidebarLinks.length; i++) {
        sidebarLinks[i].addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                var sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.remove('active');
                }
            }
        });
    }
</script>
</body>
</html>