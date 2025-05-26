import { get } from '../ajax.js';

export async function loadProductsView() {
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

function showProductForm(productId = null) {
    alert('Product form will be shown here. Product ID: ' + productId);
}
