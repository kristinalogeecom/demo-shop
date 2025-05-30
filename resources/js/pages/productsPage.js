import { HttpClient } from '../ajax.js';
const http = new HttpClient();

export async function loadProductsView() {
    const app = document.getElementById('app');
    document.getElementById('page-title').textContent = 'Product Management';
    app.innerHTML = `<div class="loading">Loading product view...</div>`;

    try {
        const [html, products] = await Promise.all([
            fetch('/admin/products').then(res => res.text()),
            http.get('/admin/products/data')
        ]);

        app.innerHTML = html;

        const tbody = document.getElementById('productsTableBody');
        tbody.innerHTML = products.map(product => `
            <tr>
                <td><input type="checkbox" data-id="${product.id}"></td>
                <td>${product.title}</td>
                <td>${product.sku}</td>
                <td>${product.brand}</td>
                <td>${product.category}</td>
                <td>${product.short_description}</td>
                <td>${parseFloat(product.price).toLocaleString()}</td>
                <td><input type="checkbox" disabled ${product.enabled ? 'checked' : ''}></td>
                <td class="action-cell">
                    <button class="btn btn-sm edit" data-id="${product.id}">✏️</button>
                    <button class="btn btn-sm btn-danger delete" data-id="${product.id}">🗑️</button>
                </td>
            </tr>
        `).join('');

        document.getElementById('addProduct').addEventListener('click', () => showProductForm());

        document.querySelectorAll('.edit').forEach(btn => {
            btn.addEventListener('click', () => showProductForm(btn.dataset.id));
        });

        document.querySelectorAll('.delete').forEach(btn => {
            btn.addEventListener('click', () => deleteProduct(btn.dataset.id));
        });

        document.getElementById('productForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            await fetch('/admin/products/save', { method: 'POST', body: formData });
            await loadProductsView();
        });

        document.getElementById('cancelProduct').addEventListener('click', () => {
            document.getElementById('productModal').classList.add('hidden');
        });

    } catch (e) {
        app.innerHTML = `<div class="error">Error: ${e.message}</div>`;
    }
}

function showProductForm(productId = null) {
    const modal = document.getElementById('productModal');
    modal.classList.remove('hidden');
    if (productId) {
        // if it is edit, fill from database
    } else {
        document.getElementById('productForm').reset();
    }
}

function deleteProduct(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        fetch(`/admin/products/delete/${productId}`, { method: 'DELETE' })
            .then(() => loadProductsView());
    }
}
