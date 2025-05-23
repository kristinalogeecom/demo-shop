import { get } from './ajax.js';
import { router } from './router.js';

router.addRoute('dashboard', loadDashboardStats);
router.addRoute('products', loadProductsView);
router.addRoute('categories', loadCategoriesView);

async function loadDashboardStats() {
    try {
        const data = await get('/admin/dashboard/data');
        const app = document.getElementById('app');

        // Update page title
        document.getElementById('page-title').textContent = 'Admin Dashboard';

        app.innerHTML = `
            <div class="stats-container">
                <!-- Prvi red - Products i Categories -->
                <div class="stats-row">
                    <div class="stat-item">
                        <label>Products count</label>
                        <input type="text" value="${data.productsCount}" readonly>
                    </div>
                    <div class="stat-item">
                        <label>Categories count</label>
                        <input type="text" value="${data.categoriesCount}" readonly>
                    </div>
                </div>
                
                <!-- Drugi red - Ostala tri polja -->
                <div class="stats-row">
                    <div class="stat-item">
                        <label>Home page opening count</label>
                        <input type="text" value="${data.homePageViews}" readonly>
                    </div>
                    <div class="stat-item">
                        <label>Most often viewed product</label>
                        <input type="text" value="${data.mostViewedProduct}" readonly>
                    </div>
                    <div class="stat-item">
                        <label>Number of views</label>
                        <input type="text" value="${data.mostViewedProductViews}" readonly>
                    </div>
                </div>
            </div>
        `;

    } catch (error) {
        const app = document.getElementById('app');
        app.innerHTML = `
            <div class="error">
                <h2>Loading Error</h2>
                <p>${error.message}</p>
                <button class="btn btn-primary" onclick="window.location.reload()">Retry</button>
            </div>
        `;
    }
}


async function loadProductsView() {
    const products = await get('/admin/products');
    const app = document.getElementById('app');

    document.getElementById('page-title').textContent = 'Product Management';

    app.innerHTML = `
        <div class="action-buttons">
            <button class="btn btn-primary" id="addProduct">Add Product</button>
        </div>
        <div class="data-table-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${products.map(product => `
                        <tr>
                            <td>${product.id}</td>
                            <td>${product.name}</td>
                            <td>$${product.price.toFixed(2)}</td>
                            <td>${product.category}</td>
                            <td>${product.stock}</td>
                            <td class="action-cell">
                                <button class="btn btn-sm" data-id="${product.id}">Edit</button>
                                <button class="btn btn-sm btn-danger" data-id="${product.id}">Delete</button>
                            </td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>
    `;


    document.getElementById('addProduct').addEventListener('click', showProductForm);
    document.querySelectorAll('.edit').forEach(btn => {
        btn.addEventListener('click', () => showProductForm(btn.dataset.id));
    });

}

async function loadCategoriesView() {
    try {
        const categories = await get('/admin/categories');
        const app = document.getElementById('app');

        // Update page title
        document.getElementById('page-title').textContent = 'Product Categories';

        app.innerHTML = `
            <div class="action-buttons">
                <button class="btn btn-primary" id="addCategory">
                    <i class="fas fa-plus"></i> Add Category
                </button>
            </div>
            <div class="data-table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${categories.map(category => `
                            <tr>
                                <td>${category.id}</td>
                                <td>${category.name}</td>
                                <td>${category.slug}</td>
                                <td>${category.product_count}</td>
                                <td>
                                    <span class="status-badge ${category.active ? 'active' : 'inactive'}">
                                        ${category.active ? 'Active' : 'Inactive'}
                                    </span>
                                </td>
                                <td class="action-cell">
                                    <button class="btn btn-sm" data-id="${category.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" data-id="${category.id}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;


    } catch (error) {
        const app = document.getElementById('app');
        app.innerHTML = `
            <div class="error">
                <h2>Loading Error</h2>
                <p>${error.message}</p>
                <button class="btn btn-primary" onclick="window.location.reload()">
                    <i class="fas fa-sync-alt"></i> Retry
                </button>
            </div>
        `;
    }
}

function showProductForm(productId = null) {

}

document.addEventListener('DOMContentLoaded', () => {
    // First verify the router is ready
    if (!router) {
        console.error('Router not initialized');
        return;
    }

    const currentRoute = location.hash.slice(1) || 'dashboard';
    document.querySelector(`[data-route="${currentRoute}"]`).classList.add('active');

    router.navigateTo(currentRoute);
});