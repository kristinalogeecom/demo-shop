import {router} from './router.js';

import {loadDashboardStats} from './pages/dashboardPage.js';
import {loadProductsView} from './pages/productsPage.js';
import {loadCategoriesView} from './pages/categories/categoriesPage.js';

router.addRoute('dashboard', loadDashboardStats);
router.addRoute('products', loadProductsView);
router.addRoute('categories', loadCategoriesView);

document.addEventListener('DOMContentLoaded', () => {
    const currentRoute = location.hash.slice(1) || 'dashboard';

    document.querySelector(`[data-route="${currentRoute}"]`)?.classList.add('active');
    router.navigateTo(currentRoute);

    // Logout handler
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const confirmed = confirm("Are you sure you want to log out?");
            if (!confirmed) return;

            try {
                const response = await fetch('/admin/logout', {
                    method: 'POST'
                });

                if (response.redirected) {
                    window.location.href = response.url;
                } else {
                    window.location.href = '/admin/login';
                }
            } catch (error) {
                alert('Logout failed.');
                console.error(error);
            }
        });
    }
});