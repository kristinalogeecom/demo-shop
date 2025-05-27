import {
    renderCategoryTree,
    renderCategoryDetails,
    renderNoSelectionMessage,
    renderErrorMessage,
    renderCategoryForm
} from './categoryRenderer.js';
import {
    fetchCategories,
    fetchCategory,
    fetchFlatCategories,
    saveCategory,
    deleteCategoryById
} from './categoryService.js';

export async function loadCategoriesView() {
    try {
        const categories = await fetchCategories();
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
                    ${renderNoSelectionMessage()}
                </div>
            </div>
        `;

        addCategoryTreeListeners();
        document.getElementById('addRootCategory').addEventListener('click', () => renderCategoryFormPanel());

    } catch (error) {
        document.getElementById('app').innerHTML = renderErrorMessage(error.message);
    }
}

function addCategoryTreeListeners() {
    document.querySelectorAll('.category-item').forEach(item => {
        item.addEventListener('click', async (e) => {
            const categoryId = e.currentTarget.dataset.id;
            document.querySelectorAll('.category-item').forEach(i => i.classList.remove('active'));
            e.currentTarget.classList.add('active');
            const panel = document.getElementById('categoryDetails');
            panel.innerHTML = await renderCategoryDetails(categoryId, renderCategoryFormPanel, handleDeleteCategory);

            bindCategoryDetailActions(categoryId);
        });
    });

    document.querySelectorAll('.expand-icon').forEach(icon => {
        icon.addEventListener('click', (e) => {
            e.stopPropagation();
            const wrapper = e.target.closest('.category-item-wrapper');
            const childContainer = wrapper.querySelector('.category-children');

            if (!childContainer) return;

            const isVisible = childContainer.style.display === 'block';
            childContainer.style.display = isVisible ? 'none' : 'block';
            e.target.classList.toggle('fa-plus-square', isVisible);
            e.target.classList.toggle('fa-minus-square', !isVisible);
        });
    });
}

async function renderCategoryFormPanel(categoryId = null, parentId = null) {
    disableUI();
    const panel = document.getElementById('categoryDetails');
    const selectedCategoryId = document.querySelector('.category-item.active')?.dataset.id || null;
    const allCategories = await fetchFlatCategories();

    panel.innerHTML = renderCategoryForm({ categoryId, parentId, allCategories });

    const parentSelect = document.getElementById('parentSelect');
    if (!categoryId && !parentId) parentSelect.disabled = true;
    if (!categoryId && parentId) {
        parentSelect.disabled = true;
        parentSelect.value = parentId;
    }

    document.getElementById('cancelCategoryForm').addEventListener('click', async () => {
        enableUI();
        if (selectedCategoryId) {
            panel.innerHTML = await renderCategoryDetails(selectedCategoryId, renderCategoryFormPanel, handleDeleteCategory);
            bindCategoryDetailActions(selectedCategoryId);
        } else {
            panel.innerHTML = renderNoSelectionMessage();
        }
    });

    document.getElementById('inlineCategoryForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const data = {
            id: document.getElementById('categoryId').value || null,
            parent_id: document.getElementById('parentSelect').value || null,
            name: document.getElementById('categoryName').value,
            code: document.getElementById('categoryCode').value,
            description: document.getElementById('categoryDescription').value
        };
        if (!data.name.trim()) return alert('Category name is required.');

        const saved = await saveCategory(data);
        await refreshCategoriesTree(saved.id, saved.parent_id);
        enableUI();
    });

    if (categoryId) {
        const category = await fetchCategory(categoryId);
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categoryCode').value = category.code || '';
        document.getElementById('categoryDescription').value = category.description || '';
        document.getElementById('parentSelect').value = category.parent_id ?? '';
        document.getElementById('parentSelect').disabled = false;
    }
}

async function handleDeleteCategory(categoryId) {
    const selectedCategoryId = document.querySelector('.category-item.active')?.dataset.id || null;
    const shouldClearPanel = selectedCategoryId === String(categoryId);

    if (!confirm('Are you sure you want to delete this category?')) return;
    await deleteCategoryById(categoryId);
    await refreshCategoriesTree(shouldClearPanel ? null : selectedCategoryId);

    if (shouldClearPanel) {
        document.getElementById('categoryDetails').innerHTML = renderNoSelectionMessage();
    }
}

async function refreshCategoriesTree(selectedCategoryId = null, expandParentId = null) {
    const expandedIds = getExpandedCategoryIds();
    if (expandParentId && !expandedIds.includes(String(expandParentId))) {
        expandedIds.push(String(expandParentId));
    }

    const categories = await fetchCategories();
    const treeEl = document.getElementById('categoriesTree');
    treeEl.innerHTML = renderCategoryTree(categories);
    addCategoryTreeListeners();

    expandedIds.forEach(id => {
        const wrapper = document.querySelector(`.category-item-wrapper[data-id="${id}"]`);
        const icon = wrapper?.querySelector('.expand-icon');
        const child = wrapper?.querySelector('.category-children');
        if (child) {
            child.style.display = 'block';
            icon?.classList.remove('fa-plus-square');
            icon?.classList.add('fa-minus-square');
        }
    });

    if (selectedCategoryId) {
        const selectedEl = document.querySelector(`.category-item[data-id="${selectedCategoryId}"]`);
        if (selectedEl) {
            selectedEl.classList.add('active');
            const panel = document.getElementById('categoryDetails');
            panel.innerHTML = await renderCategoryDetails(selectedCategoryId);
            bindCategoryDetailActions(selectedCategoryId);
        }
    }
}

function getExpandedCategoryIds() {
    const expanded = [];
    document.querySelectorAll('.category-children').forEach(child => {
        if (child.style.display === 'block') {
            const wrapper = child.closest('.category-item-wrapper');
            if (wrapper?.dataset.id) {
                expanded.push(wrapper.dataset.id);
            }
        }
    });
    return expanded;
}

function disableUI() {
    document.querySelector('.categories-tree')?.classList.add('disabled-overlay');
    document.querySelector('.action-buttons')?.classList.add('disabled-overlay');
}

function enableUI() {
    document.querySelector('.categories-tree')?.classList.remove('disabled-overlay');
    document.querySelector('.action-buttons')?.classList.remove('disabled-overlay');
}

function bindCategoryDetailActions(categoryId) {
    document.getElementById('addSubcategory')?.addEventListener('click', () => renderCategoryFormPanel(null, categoryId));
    document.getElementById('editCategory')?.addEventListener('click', () => renderCategoryFormPanel(categoryId));
    document.getElementById('deleteCategory')?.addEventListener('click', () => handleDeleteCategory(categoryId));
}

