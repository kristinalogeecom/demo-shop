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

function showProductForm(productId = null) {
    alert('Product form will be shown here. Product ID: ' + productId);
}



async function loadCategoriesView() {
    try {
        const categories = await get('/admin/categories');
        const app = document.getElementById('app');

        // Update page title
        document.getElementById('page-title').textContent = 'Product Categories';

        app.innerHTML = `
            <div class="action-buttons">
                <button class="btn btn-primary" id="addRootCategory">
                    <i class="fas fa-plus"></i> Add Root Category
                </button>
            </div>
            <div class="categories-container">
                <div class="categories-tree" id="categoriesTree">
                    ${renderCategoryTree(categories)}
                </div>
                <div class="category-details" id="categoryDetails">
                    <div class="no-selection">
                        <i class="fas fa-info-circle"></i> Select a category to view details
                    </div>
                </div>
            </div>
        `;

        // Add event listeners for category items
        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const categoryId = e.currentTarget.dataset.id;
                loadCategoryDetails(categoryId);

                // Update active state
                document.querySelectorAll('.category-item').forEach(i => {
                    i.classList.remove('active');
                });
                e.currentTarget.classList.add('active');
            });
        });

        // Add root category button
        document.getElementById('addRootCategory').addEventListener('click', () => {
            renderCategoryFormInPanel();
        });

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
function renderCategoryTree(categories, level = 0) {
    return categories.map(category => `
        <div class="category-item-wrapper" style="margin-left: ${level * 20}px">
            <div class="category-item" data-id="${category.id}">
                <div>
                    <i class="fas ${category.children && category.children.length > 0 ? 'fa-folder-open' : 'fa-folder'}"></i>
                    ${category.name}
                </div>
            </div>
            ${category.children && category.children.length > 0
        ? renderCategoryTree(category.children, level + 1)
        : ''
    }
        </div>
    `).join('');
}

async function loadCategoryDetails(categoryId) {
    try {
        const category = await get(`/admin/categories/${categoryId}`);

        const detailsPanel = document.getElementById('categoryDetails');
        detailsPanel.innerHTML = `
            <h3>Selected Category</h3>
            <div class="detail-row">
                <span class="detail-label">Title:</span>
                <div class="detail-value">${category.name}</div>
            </div>
            <div class="detail-row">
                <span class="detail-label">Parent category:</span>
                <div class="detail-value">${category.parent?.name || 'Root Category'}</div>
            </div>
            <div class="detail-row">
                <span class="detail-label">Code:</span>
                <div class="detail-value">${category.code || 'N/A'}</div>
            </div>
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <div class="detail-value">${category.description || 'No description provided'}</div>
            </div>
            <div class="category-actions">
                <button class="btn btn-primary" id="addSubcategory">
                    <i class="fas fa-plus"></i> Add Subcategory
                </button>
                <button class="btn" id="editCategory">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger" id="deleteCategory">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        `;

        document.getElementById('addSubcategory').addEventListener('click', () => {
            renderCategoryFormInPanel(null, category.id);
        });
        document.getElementById('editCategory').addEventListener('click', () => {
            renderCategoryFormInPanel(category.id);
        });


        document.getElementById('deleteCategory').addEventListener('click', () => {
            deleteCategory(category.id);
        });

    } catch (error) {
        console.error('Error loading category details:', error);
    }
}

function renderCategoryFormInPanel(categoryId = null, parentId = null) {
    const panel = document.getElementById('categoryDetails');

    panel.innerHTML = `
        <h3>${categoryId ? 'Edit Category' : parentId ? 'Add Subcategory' : 'Add Root Category'}</h3>
        <form id="inlineCategoryForm">
            <input type="hidden" id="categoryId" value="${categoryId || ''}">
            <input type="hidden" id="parentId" value="${parentId || ''}">

            <div class="detail-row">
                <label class="detail-label" for="categoryName">Title:</label>
                <input class="detail-input" type="text" id="categoryName" required>
            </div>
            <div class="detail-row">
                <label class="detail-label" for="categoryCode">Code:</label>
                <input class="detail-input" type="text" id="categoryCode">
            </div>
            <div class="detail-row">
                <label class="detail-label" for="categoryDescription">Description:</label>
                <textarea class="detail-input" id="categoryDescription" rows="4"></textarea>
            </div>

            <div class="category-actions">
                <button type="submit" class="btn btn-primary">
                    ${categoryId ? 'Update' : 'Save'}
                </button>
                <button type="button" class="btn btn-cancel" id="cancelCategoryForm">Cancel</button>
            </div>
        </form>
    `;

    // Cancel
    document.getElementById('cancelCategoryForm').addEventListener('click', () => {
        panel.innerHTML = `
            <div class="no-selection">
                <i class="fas fa-info-circle"></i> Select a category to view details
            </div>
        `;
    });

    // Submit
    document.getElementById('inlineCategoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        await saveCategory({
            id: document.getElementById('categoryId').value || null,
            parent_id: document.getElementById('parentId').value || null,
            name: document.getElementById('categoryName').value,
            code: document.getElementById('categoryCode').value,
            description: document.getElementById('categoryDescription').value
        });
        loadCategoriesView(); // refresh list
    });

    // Ako je edit, popuni podatke
    if (categoryId) {
        loadCategoryData(categoryId).then(category => {
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryCode').value = category.code || '';
            document.getElementById('categoryDescription').value = category.description || '';
        });
    }
}


function showCategoryForm(categoryId = null, parentId = null) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';

    modal.innerHTML = `
        <div class="modal-content">
            <div class="modal-header">
                <h3>${categoryId ? 'Edit Category' : parentId ? 'Add Subcategory' : 'Add Root Category'}</h3>
                <button class="close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <form id="categoryForm">
                    <input type="hidden" id="categoryId" value="${categoryId || ''}">
                    <input type="hidden" id="parentId" value="${parentId || ''}">
                    <div class="form-group">
                        <label for="categoryName">Category Name *</label>
                        <input type="text" id="categoryName" required>
                    </div>
                    <div class="form-group">
                        <label for="categoryCode">Code</label>
                        <input type="text" id="categoryCode">
                    </div>
                    <div class="form-group">
                        <label for="categoryDescription">Description</label>
                        <textarea id="categoryDescription" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            ${categoryId ? 'Update' : 'Save'}
                        </button>
                        <button type="button" class="btn btn-cancel">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    // Close modal handlers
    modal.querySelector('.close-btn').addEventListener('click', () => modal.remove());
    modal.querySelector('.btn-cancel').addEventListener('click', () => modal.remove());

    // Form submission
    modal.querySelector('#categoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        await saveCategory({
            id: document.getElementById('categoryId').value || null,
            parent_id: document.getElementById('parentId').value || null,
            name: document.getElementById('categoryName').value,
            code: document.getElementById('categoryCode').value,
            description: document.getElementById('categoryDescription').value
        });
        modal.remove();
        loadCategoriesView(); // Refresh the view
    });

    // If editing, load category data
    if (categoryId) {
        loadCategoryData(categoryId).then(category => {
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryCode').value = category.code || '';
            document.getElementById('categoryDescription').value = category.description || '';
        });
    }
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