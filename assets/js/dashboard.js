import { router } from './router.js';

import { loadDashboardStats } from './pages/dashboardPage.js';
import { loadProductsView } from './pages/productsPage.js';
import { loadCategoriesView } from './pages/categoriesPage.js';

router.addRoute('dashboard', loadDashboardStats);
router.addRoute('products', loadProductsView);
router.addRoute('categories', loadCategoriesView);

document.addEventListener('DOMContentLoaded', () => {
    const currentRoute = location.hash.slice(1) || 'dashboard';

    document.querySelector(`[data-route="${currentRoute}"]`)?.classList.add('active');
    router.navigateTo(currentRoute);
});