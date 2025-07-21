 </div>
        <!-- main @e -->
    </div>

    <script src="./assets/js/bundle.js?ver=3.3.0"></script>
    <script src="./assets/js/scripts.js?ver=3.3.0"></script>
    <script src="./assets/js/charts/gd-campaign.js?ver=3.3.0"></script>
                <script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const currentPath = window.location.pathname.split('/').pop() || 'index.php';
        const menuItems = {
            'index.php': 'menu-casa',
            'pacientes.html': 'menu-pacientes',
            'citas.html': 'menu-citas',
            'areas.html': 'menu-areas',
            'evaluaciones.html': 'menu-evaluaciones'
        };
        
        const activeItemId = menuItems[currentPath];
        if (activeItemId) {
            const activeElement = document.getElementById(activeItemId);
            if (activeElement) {
                activeElement.classList.add('active');
            }
        }
    }, 50); // Pequeño delay para que se ejecute después de otros scripts
});
</script>
</body>

</html>