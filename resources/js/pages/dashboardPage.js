import { HttpClient } from '../ajax.js';
const http = new HttpClient();

export async function loadDashboardStats() {
    const app = document.getElementById('app');
    document.getElementById('page-title').textContent = 'Admin Dashboard';

    app.innerHTML = `<div class="loading">Loading dashboard...</div>`;

    try {
        const [html, data] = await Promise.all([
            fetch('/admin/dashboard/view').then(res => {
                if (!res.ok) throw new Error('Failed to load dashboard HTML');
                return res.text();
            }),
            http.get('/admin/dashboard/data')
        ]);

        app.innerHTML = html;

        requestAnimationFrame(() => {
            try {
                const set = (id, value) => {
                    const el = document.getElementById(id);
                    if (el) el.value = value;
                };

                set('productsCount', data.productsCount);
                set('categoriesCount', data.categoriesCount);
                set('homePageViews', data.homePageViews);
                set('mostViewedProduct', data.mostViewedProduct);
                set('mostViewedProductViews', data.mostViewedProductViews);
            } catch (renderErr) {
                console.error(renderErr);
                app.innerHTML = `
                    <div class="error">
                        <h2>Render Error</h2>
                        <p>${renderErr.message}</p>
                        <button class="btn btn-primary" onclick="window.location.reload()">Retry</button>
                    </div>
                `;
            }
        });

    } catch (error) {
        console.error(error);
        app.innerHTML = `
            <div class="error">
                <h2>Loading Error</h2>
                <p>${error.message}</p>
                <button class="btn btn-primary" onclick="window.location.reload()">Retry</button>
            </div>
        `;
    }
}
