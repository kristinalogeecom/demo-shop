import { HttpClient } from '../ajax.js';
const http = new HttpClient();

export async function loadProductsView() {
    const app = document.getElementById('app');
    document.getElementById('page-title').textContent = 'Product Management';

    app.innerHTML = `<div class="loading">Loading product view...</div>`;

    try {
        const [html, products] = await Promise.all([
            fetch('/admin/products').then(res => {
                if (!res.ok) throw new Error('Failed to load products HTML');
                return res.text();
            }),
            http.get('/admin/products/data')
        ]);

        app.innerHTML = html;

        const tbody = document.getElementById('productsTableBody');
        tbody.innerHTML = products.map(product => `
            <tr>
                <td>${product.id}</td>
                <td>${product.name}</td>
                <td>$${product.price.toFixed(2)}</td>
                <td>${product.category ?? '-'}</td>
                <td>${product.stock ?? '-'}</td>
                <td class="action-cell">
                    <button class="btn btn-sm edit" data-id="${product.id}">Edit</button>
                    <button class="btn btn-sm btn-danger" data-id="${product.id}">Delete</button>
                </td>
            </tr>
        `).join('');

        document.getElementById('addProduct').addEventListener('click', showProductForm);
        document.querySelectorAll('.edit').forEach(btn => {
            btn.addEventListener('click', () => showProductForm(btn.dataset.id));
        });

    } catch (error) {
        app.innerHTML = `
            <div class="error">
                <h2>Loading Error</h2>
                <p>${error.message}</p>
                <button class="btn btn-primary" onclick="window.location.reload()">Retry</button>
            </div>
        `;
    }
}

function showProductForm(productId = null) {
    alert('Product form will be shown here. Product ID: ' + productId);
}
