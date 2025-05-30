import { router } from './router.js';
import { HttpClient } from './ajax.js';

import { loadDashboardStats } from './pages/dashboardPage.js';
import { loadProductsView } from './pages/productsPage.js';
import { loadCategoriesView } from './pages/categories/categoriesPage.js';

const http = new HttpClient();

router.addRoute('dashboard', loadDashboardStats);
router.addRoute('products', loadProductsView);
router.addRoute('categories', loadCategoriesView);
document.addEventListener('DOMContentLoaded', async () => {
    await initApp();
});

async function initApp() {
    const currentRoute = location.hash.slice(1) || 'dashboard';

    // Set active class for current menu item
    document.querySelector(`[data-route="${currentRoute}"]`)?.classList.add('active');

    // Pokreni rutu
    await router.navigateTo(currentRoute);

    // Attach logout handler (nakon što DOM postoji!)
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const confirmed = confirm("Are you sure you want to log out?");
            if (!confirmed) return;

            try {
                const response = await http.simplePost('/admin/logout');

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
}
