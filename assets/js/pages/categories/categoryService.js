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
        if (!response.success) throw new Error(response.error || 'Save failed');
        return response.category;
    } catch (error) {
        console.error('Error saving category:', error);
        if (error.message.includes('Duplicate entry') && error.message.includes('code_UNIQUE')) {
            alert('Error: A category with this code already exists. Please use a different code.');
        } else {
            alert('Request failed: ' + error.message);
        }
        throw error;
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
export function filterParentOptions(allCategories, excludeIds) {
    return allCategories.filter(cat => !excludeIds.includes(cat.id));
}



