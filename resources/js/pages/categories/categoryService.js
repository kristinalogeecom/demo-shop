import { HttpClient } from '../../ajax.js';

export class CategoryService {
    constructor() {
        this.http = new HttpClient();
    }

    async fetchCategories() {
        return await this.http.get('/admin/categories');
    }

    async fetchCategory(categoryId) {
        return await this.http.get(`/admin/categories/${categoryId}`);
    }

    async fetchFlatCategories() {
        return await this.http.get('/admin/categories-flat');
    }

    async fetchDescendantCategoryIds(categoryId) {
        return await this.http.get(`/admin/categories/${categoryId}/descendants`);
    }

    async saveCategory(data) {
        try {
            const response = await this.http.post('/admin/categories/save', data);

            if (response.errors) {
                throw new Error(response.errors.join('\n'));
            }

            if (!response.success) {
                throw new Error(response.error || 'Save failed');
            }

            return response.category;
        } catch (error) {
            throw new Error(error?.message || 'Unexpected error while saving category.');
        }
    }

    async deleteCategoryById(categoryId) {
        try {
            const response = await this.http.post('/admin/categories/delete', { id: categoryId });
            if (!response.success) {
                throw new Error(response.error || 'Delete failed');
            }
        } catch (error) {
            console.error('Delete error: ', error);
            alert('Request failed: ' + error.message);
            throw error;
        }
    }
}
