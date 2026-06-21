<!-- Cerrar el div del contenido principal si el usuario esta autenticado -->
<?php if (isset($_SESSION['user_id'])): ?>
    </div>
<?php endif; ?>

<!-- Script para cerrar el sidebar automaticamente en dispositivos moviles al hacer clic en un enlace -->
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