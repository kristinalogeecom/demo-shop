import {get, post} from '../ajax.js';

export async function loadCategoriesView() {
    try {
        const categories = await get('/admin/categories');
        const app = document.getElementById('app');

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

        document.querySelectorAll('.category-item').forEach(item => {
            item.addEventListener('click', (e) => {
                const categoryId = e.currentTarget.dataset.id;
                loadCategoryDetails(categoryId);
                document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
                e.currentTarget.classList.add('active');
            });
        });

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
                    <i class="fas ${category.children?.length > 0 ? 'fa-folder-open' : 'fa-folder'}"></i>
                    ${category.name}
                </div>
            </div>
            ${category.children?.length > 0 ? renderCategoryTree(category.children, level + 1) : ''}
        </div>
    `).join('');
}

async function loadCategoryDetails(categoryId) {
    try {
        const category = await get(`/admin/categories/${categoryId}`);
        const panel = document.getElementById('categoryDetails');

        panel.innerHTML = `
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
                <button class="btn btn-primary" id="addSubcategory">Add Subcategory</button>
                <button class="btn" id="editCategory">Edit</button>
                <button class="btn btn-danger" id="deleteCategory">Delete</button>
            </div>
        `;

        document.getElementById('addSubcategory').addEventListener('click', () => renderCategoryFormInPanel(null, category.id));
        document.getElementById('editCategory').addEventListener('click', () => renderCategoryFormInPanel(category.id));
        document.getElementById('deleteCategory').addEventListener('click', () => deleteCategory(category.id));

    } catch (error) {
        console.error('Error loading category details:', error);
    }
}

function renderCategoryFormInPanel(categoryId = null, parentId = null) {

    // Disable all other UI parts
    document.querySelector('.categories-tree').classList.add('disabled-overlay');
    document.querySelector('.action-buttons').classList.add('disabled-overlay');

    const panel = document.getElementById('categoryDetails');

    const selectedCategoryId = document.querySelector('.category-item.active')?.dataset.id || null;


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
    document.getElementById('cancelCategoryForm').addEventListener('click', async () => {
        document.querySelector('.categories-tree').classList.remove('disabled-overlay');
        document.querySelector('.action-buttons').classList.remove('disabled-overlay');

        if (selectedCategoryId) {
            await loadCategoryDetails(selectedCategoryId);
        } else {
            panel.innerHTML = `<div class="no-selection"><i class="fas fa-info-circle"></i> Select a category to view details</div>`;
        }
    });


    document.getElementById('inlineCategoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            id: document.getElementById('categoryId').value || null,
            parent_id: document.getElementById('parentId').value || null,
            name: document.getElementById('categoryName').value,
            code: document.getElementById('categoryCode').value,
            description: document.getElementById('categoryDescription').value
        };
        await saveCategory(data);
        await loadCategoriesView();

        document.querySelector('.categories-tree').classList.remove('disabled-overlay');
        document.querySelector('.action-buttons').classList.remove('disabled-overlay');

    });

    if (categoryId) {
        loadCategoryData(categoryId).then(category => {
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryCode').value = category.code || '';
            document.getElementById('categoryDescription').value = category.description || '';
            document.getElementById('parentId').value = category.parent_id ?? '';
        });
    }
}

async function saveCategory(data) {
    try {
        const response = await post('/admin/categories/save', data);
        if (response.success) {
            console.log('Category saved:', response.category);
        } else {
            console.error('Save failed', response);
            alert('Error: ' + (response.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error saving category:', error);
        alert('Request failed: ' + error.message);
    }
}

async function loadCategoryData(categoryId) {
    return await get(`/admin/categories/${categoryId}`);
}

async function deleteCategory(categoryId) {
    const confirmed = confirm('Are you sure you want to delete this category?');
    if(!confirmed) return;

    try {
        const response = await post('/admin/categories/delete', { id: categoryId });
        if(response.success) {
            await loadCategoriesView();
        } else {
            alert(response.error || 'Deletion failed.');
        }
    } catch (error) {
        console.error('Delete error: ', error);
        alert('Request failed: ' + error.message);
    }
}
