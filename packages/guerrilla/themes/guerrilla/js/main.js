/**
 * Guerrilla – Main JavaScript
 * Concrete CMS 9.5 Theme
 */

'use strict';

(function () {
    // Bootstrap 5 is loaded separately via asset manager.
    // Place custom JS logic here.

    document.addEventListener('DOMContentLoaded', function () {
        // ---- Active nav link highlight ----
        const currentPath = window.location.pathname;
        document.querySelectorAll('#navbarMain .nav-link').forEach(function (link) {
            const href = link.getAttribute('href');
            if (href && currentPath.startsWith(href) && href !== '/') {
                link.classList.add('active');
                link.setAttribute('aria-current', 'page');
            }
        });

        // ---- Smooth scroll for anchor links ----
        document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
            anchor.addEventListener('click', function (e) {
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    });
})();
