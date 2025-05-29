import { get, post } from '../../ajax.js';

export async function fetchCategories() {
    return await get('/admin/categories');
}

export async function fetchCategory(categoryId) {
    return await get(`/admin/categories/${categoryId}`);
}

export async function fetchFlatCategories() {
    return await get('/admin/categories-flat');
}

export async function fetchDescendantCategoryIds(categoryId) {
    return await get(`/admin/categories/${categoryId}/descendants`);
}

export async function saveCategory(data) {
    try {
        const response = await post('/admin/categories/save', data);
        if (!response.success) {
            throw new Error(response.error || 'Save failed');
        }

        return response.category;

    } catch (error) {
        if (error?.message) {
            throw new Error(error.message);
        }

        throw new Error('Unexpected error while saving category.');
    }
}

export async function deleteCategoryById(categoryId) {
    try {
        const response = await post('/admin/categories/delete', { id: categoryId });
        if (!response.success) throw new Error(response.error || 'Delete failed');
    } catch (error) {
        console.error('Delete error: ', error);
        alert('Request failed: ' + error.message);
        throw error;
    }
}


