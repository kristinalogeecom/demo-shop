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
        const data = await this.http.get(`/admin/categories/${categoryId}/descendants`);
        return Array.isArray(data) ? data.map(id => Number(id)) : [];
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
                const message =
                    response.error ||
                    (Array.isArray(response.errors) ? response.errors.join('\n') : null) ||
                    'Delete failed';

                alert(message);
                throw new Error(message);

            }

        } catch (error) {
            console.error('Delete error: ', error);
            throw error;
        }
    }

}
