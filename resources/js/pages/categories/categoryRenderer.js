import { CategoryService } from './categoryService.js';
const categoryService = new CategoryService();

export function renderCategoryTree(categories, level = 0) {
    return categories.map(category => {
        const hasChildren = category.children?.length > 0;
        const childTree = hasChildren ? `<div class="category-children" style="display:none;">${renderCategoryTree(category.children, level + 1)}</div>` : '';

        return `
            <div class="category-item-wrapper" data-id="${category.id}" style="margin-left: ${level * 10}px">
                <div class="category-item" data-id="${category.id}">
                    <div>
                        ${hasChildren ? `<i class="expand-icon fas fa-plus-square" data-id="${category.id}"></i>` : '<span class="expand-icon"></span>'}
                        <span class="category-name">${category.name}</span>
                    </div>
                </div>
                ${childTree}
            </div>
        `;
    }).join('');
}

export async function renderCategoryDetails(categoryId) {
    const category = await categoryService.fetchCategory(categoryId);
    return `
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
}

export function renderNoSelectionMessage() {
    return `
        <div class="no-selection-message">
            <i class="fas fa-info-circle"></i>
            <p>Select a category to view details</p>
        </div>
    `;
}

export function renderErrorMessage(message) {
    return `
        <div class="error">
            <h2>Loading Error</h2>
            <p>${message}</p>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-sync-alt"></i> Retry
            </button>
        </div>
    `;
}

export function renderCategoryForm({ categoryId, parentId, allCategories }) {
    return `
        <h3>${categoryId ? 'Edit Category' : parentId ? 'Add Subcategory' : 'Add Root Category'}</h3>
        <form id="inlineCategoryForm">
            <input type="hidden" id="categoryId" value="${categoryId || ''}">
            <div class="detail-row">
                <label class="detail-label" for="categoryName">Title:</label>
                <input class="detail-input" type="text" id="categoryName">
            </div>
            <div class="detail-row">
                <label class="detail-label" for="parentSelect">Parent category:</label>
                <select class="detail-input" id="parentSelect">
                    <option value="">Root Category</option>
                    ${allCategories.map(cat => `<option value="${cat.id}">${cat.name}</option>`).join('')}
                </select>
            </div>
            <div class="detail-row">
                <label class="detail-label" for="categoryCode">Code:</label>
                <input class="detail-input" type="text" id="categoryCode">
            </div>
            <div class="detail-row">
                <label class="detail-label" for="categoryDescription">Description:</label>
                <textarea class="detail-input" id="categoryDescription" rows="4"></textarea>
            </div>
            <div id="formErrorMessage" class="form-error-message" style="color:red; margin-top: 10px;"></div>
            <div class="category-actions">
                <button type="submit" class="btn btn-primary">
                    ${categoryId ? 'Update' : 'Save'}
                </button>
                <button type="button" class="btn btn-cancel" id="cancelCategoryForm">Cancel</button>
            </div>
        </form>
    `;
}
