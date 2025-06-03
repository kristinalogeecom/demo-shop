import { ProductService } from './productService.js';

const productService = new ProductService();

export async function loadProductsView(page = 1, filters = '') {
    const app = document.getElementById('app');
    document.getElementById('page-title').textContent = 'Product Management';
    app.innerHTML = `<div class="loading">Loading product view...</div>`;

    try {
        await populateCategoryFilter();

        const [html, data] = await Promise.all([
            productService.getProductsPageHtml(),
            productService.getProductList(page, filters)
        ]);

        const { products, total, perPage, currentPage, lastPage } = data;
        app.innerHTML = html;

        await populateCategoryFilter();

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

        document.getElementById('pageInfo').textContent = `${currentPage} / ${lastPage}`;
        document.getElementById('addProduct').onclick = () => loadProductForm();
        document.querySelectorAll('.edit').forEach(btn => btn.onclick = () => loadProductForm(btn.dataset.id));
        document.querySelectorAll('.delete').forEach(btn => btn.onclick = () => confirmDelete(btn.dataset.id));

        document.getElementById('enableSelected').onclick = () => updateEnabledSelection(true);
        document.getElementById('disableSelected').onclick = () => updateEnabledSelection(false);

        document.getElementById('deleteSelected').onclick = handleBatchDelete;

        document.getElementById('firstPage').onclick = () => loadProductsView(1, filters);
        document.getElementById('prevPage').onclick = () => loadProductsView(Math.max(1, currentPage - 1), filters);
        document.getElementById('nextPage').onclick = () => loadProductsView(Math.min(lastPage, currentPage + 1), filters);
        document.getElementById('lastPage').onclick = () => loadProductsView(lastPage, filters);

        document.getElementById('filterButton').onclick = () => {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categorySelect').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;

            const params = new URLSearchParams();
            if (search) params.append('q', search);
            if (category) params.append('category_id', category);
            if (minPrice) params.append('min_price', minPrice);
            if (maxPrice) params.append('max_price', maxPrice);

            loadProductsView(1, params.toString());
        };

    } catch (e) {
        app.innerHTML = `<div class="error">Error: ${e.message}</div>`;
    }
}

export async function loadProductForm(productId = null) {
    const app = document.getElementById('app');
    document.getElementById('page-title').textContent = productId ? 'Edit Product' : 'Add New Product';

    const html = await productService.getProductFormHtml();
    app.innerHTML = html;

    document.getElementById('cancelProduct')?.addEventListener('click', () => loadProductsView());

    const form = document.getElementById('productForm');
    const categorySelect = document.getElementById('productCategory');
    const errorBox = document.getElementById('formErrorMessage');

    try {
        const categories = await productService.getCategories();
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            categorySelect.appendChild(option);
        });
    } catch (e) {
        alert('Failed to load categories: ' + e.message);
    }

    if (productId) {
        const product = await productService.getProductById(productId);
        form.title.value = product.title;
        form.sku.value = product.sku;
        form.brand.value = product.brand;
        form.category.value = product.category;
        form.short_description.value = product.short_description;
        form.long_description.value = product.long_description;
        form.price.value = product.price;
        form.enabled.checked = product.enabled;
        form.featured.checked = product.featured;
    }

    form.onsubmit = async (e) => {
        e.preventDefault();
        const file = form.image.files[0];
        if (file && !(await validateImage(file))) return;

        const formData = new FormData(form);
        errorBox.textContent = '';

        try {
            const { status, data } = await productService.saveProduct(formData);

            if (status === 422) {
                errorBox.innerHTML = data.errors.map(err => `<div>${err}</div>`).join('');
                return;
            }

            if (status >= 400) {
                errorBox.innerHTML = `<div>${data.errors?.join(', ') || 'Unexpected error occurred.'}</div>`;
                return;
            }

            await loadProductsView();

        } catch (err) {
            errorBox.innerHTML = `<div>Unexpected error occurred.</div>`;
        }
    };
}

function confirmDelete(productId) {
    if (confirm('Are you sure you want to delete this product?')) {
        productService.deleteProduct(productId).then(() => loadProductsView());
    }
}

async function handleBatchDelete() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][data-id]:checked');
    const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

    if (ids.length === 0) {
        alert('No products selected.');
        return;
    }

    if (!confirm(`Are you sure you want to delete ${ids.length} product(s)?`)) return;

    await productService.deleteProductsBatch(ids);
    await loadProductsView();
}

async function updateEnabledSelection(status) {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][data-id]:checked');
    const ids = Array.from(checkboxes).map(cb => cb.dataset.id);

    if (ids.length === 0) {
        alert('No products selected.');
        return;
    }

    await productService.updateEnabledStatus(ids, status);
    await loadProductsView();
}

async function populateCategoryFilter() {
    try {
        const categories = await productService.getCategories();
        const select = document.getElementById('categorySelect');
        select.innerHTML = `<option value="">-- Choose category --</option>`;
        categories.forEach(cat => {
            const option = document.createElement('option');
            option.value = cat.id;
            option.textContent = cat.name;
            select.appendChild(option);
        });
    } catch (e) {
        console.warn('Could not load categories for filter');
    }
}

async function validateImage(file) {
    return new Promise((resolve) => {
        const img = new Image();
        img.onload = () => {
            const width = img.width;
            const height = img.height;
            const ratio = width / height;

            if (width < 600) {
                alert("Image must be at least 600px wide.");
                resolve(false);
            } else if (ratio < 4 / 3 || ratio > 16 / 9) {
                alert("Image aspect ratio must be between 4:3 and 16:9.");
                resolve(false);
            } else {
                resolve(true);
            }
        };
        img.onerror = () => resolve(false);
        img.src = URL.createObjectURL(file);
    });
}
