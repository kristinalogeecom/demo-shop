import { get } from './ajax.js';
import { router } from './router.js';

router.addRoute('dashboard', loadDashboardStats);
router.addRoute('products', loadProductsView);
router.addRoute('categories', loadCategoriesView);


async function loadDashboardStats() {
    try {
        const data = await get('/admin/dashboard/data')
            // .catch(console.error('Error fetching data'));
        const app = document.getElementById('app');

        app.innerHTML = `
            <div class="dashboard-header">
                <h1>Admin Dashboard</h1>
            </div>
            <div class="stats-container" id="stats">
                <div class="stat-item">
                    <label>Products count:
                        <input type="text" id="productsCount" readonly>
                    </label>
                </div>
                <div class="stat-item">
                    <label>Categories count:
                        <input type="text" id="categoriesCount" readonly>
                    </label>
                </div>
                <div class="stat-item">
                    <label>Home page opening count:
                        <input type="text" id="homePageViews" readonly>
                    </label>
                </div>
                <div class="stat-item">
                    <label>Most often viewed product:
                        <input type="text" id="mostViewedProduct" readonly>
                    </label>
                </div>
                <div class="stat-item">
                    <label>Number of views:
                        <input type="text" id="mostViewedProductViews" readonly>
                    </label>
                </div>
            </div>
        `;

        document.getElementById('productsCount').value = data.productsCount || 0;
        document.getElementById('categoriesCount').value = data.categoriesCount || 0;
        document.getElementById('homePageViews').value = data.homePageViews || 0;
        document.getElementById('mostViewedProduct').value = data.mostViewedProduct || "none";
        document.getElementById('mostViewedProductViews').value = data.mostViewedProductViews || 0;
    } catch (error) {
        const app = document.getElementById('app');
        app.innerHTML = `
            <h1>Admin Dashboard</h1>
            <div class="error">Failed to load dashboard data: ${error.message}</div>
        `;
        console.error('Dashboard load error:', error);
    }


}


async function loadProductsView() {
    const products = await get('/admin/products');
    const app = document.getElementById('app');

    app.innerHTML = `
        <h1>Products Management</h1>
        <div class="actions">
            <button id="addProduct">Add Product</button>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                ${products.map(product => `
                    <tr>
                        <td>${product.id}</td>
                        <td>${product.name}</td>
                        <td>${product.price}</td>
                        <td>
                            <button class="edit" data-id="${product.id}">Edit</button>
                            <button class="delete" data-id="${product.id}">Delete</button>
                        </td>
                    </tr>
                `).join('')}
            </tbody>
        </table>
    `;


    document.getElementById('addProduct').addEventListener('click', showProductForm);
    document.querySelectorAll('.edit').forEach(btn => {
        btn.addEventListener('click', () => showProductForm(btn.dataset.id));
    });

}

async function loadCategoriesView() {
    const categories = await get('/admin/categories');
    const app = document.getElementById('app');

    app.innerHTML = `
        <h1>Product Categories</h1>
        
    `;
}


function showProductForm(productId = null) {

}


document.addEventListener('DOMContentLoaded', () => {
    // First verify the router is ready
    if (!router) {
        console.error('Router not initialized');
        return;
    }

    // Then handle the initial route
    const initialRoute = location.hash.slice(1) || 'dashboard';
    router.navigateTo(initialRoute);
});