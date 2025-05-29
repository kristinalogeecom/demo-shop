import { get } from '../ajax.js';

export async function loadDashboardStats() {
    try {
        const data = await get('/admin/dashboard/data');
        const app = document.getElementById('app');

        document.getElementById('page-title').textContent = 'Admin Dashboard';

        app.innerHTML = `
            <div class="stats-container">
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
