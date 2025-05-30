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
    const response = await fetch(`/admin/categories/view/details/${categoryId}`);
    if (!response.ok) throw new Error('Failed to load category details view');
    return await response.text();
}


export async function renderNoSelectionMessage() {
    const response = await fetch('/admin/categories/view/empty');
    if (!response.ok) throw new Error('Failed to load empty panel');
    return await response.text();
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
export async function renderCategoryForm({ categoryId, parentId }) {
    const url = new URL('/admin/categories/view/form', window.location.origin);

    if (categoryId) url.searchParams.set('categoryId', categoryId);
    if (parentId) url.searchParams.set('parentId', parentId);

    const response = await fetch(url);
    if (!response.ok) {
        throw new Error('Failed to load category form');
    }

    return await response.text();
}