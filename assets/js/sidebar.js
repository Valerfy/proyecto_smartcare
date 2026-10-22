(function () {

    function getSidebar() {
        return document.querySelector('.sidebar');
    }

    function getOverlay() {
        let overlay = document.querySelector('.sidebar-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.className = 'sidebar-overlay';
            document.body.appendChild(overlay);
        }
        return overlay;
    }

    function openSidebar() {
        const sidebar = getSidebar();
        const overlay = getOverlay();
        if (!sidebar) return;
        sidebar.classList.add('active');
        overlay.classList.add('active');
        document.body.classList.add('sidebar-open');
    }

    function closeSidebar() {
        const sidebar = getSidebar();
        const overlay = document.querySelector('.sidebar-overlay');
        if (!sidebar) return;
        sidebar.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('sidebar-open');
    }

    window.toggleSidebar = function () {
        const sidebar = getSidebar();
        if (!sidebar) return;
        if (sidebar.classList.contains('active')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    };

    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = getSidebar();
        if (!sidebar) return;

        const overlay = getOverlay();

        overlay.addEventListener('click', closeSidebar);

        sidebar.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 768) {
                    closeSidebar();
                }
            });
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    });

})();
