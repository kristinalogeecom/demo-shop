import { HttpClient } from '../../ajax.js';

export class ProductService {
    constructor() {
        this.http = new HttpClient();
    }

    async getProductList(page = 1, filters = '') {
        return this.http.get(`/admin/products/data?page=${page}&${filters}`);
    }

    async getProductsPageHtml() {
        const res = await fetch('/admin/products');
        return res.text();
    }

    async getProductFormHtml() {
        const res = await fetch('/admin/products/view/form');
        return res.text();
    }

    async getProductById(id) {
        return this.http.get(`/admin/products/${id}`);
    }

    async getCategories() {
        return this.http.get('/admin/categories-flat');
    }

    async saveProduct(formData) {
        const res = await fetch('/admin/products/save', {
            method: 'POST',
            body: formData
        });

        const data = await res.json();
        return { status: res.status, data };
    }

    async deleteProduct(id) {
        const res = await fetch(`/admin/products/delete/${id}`, { method: 'DELETE' });
        return res.json();
    }

    async deleteProductsBatch(ids) {
        return fetch('/admin/products/delete-batch', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids })
        });
    }

    async updateEnabledStatus(ids, status) {
        return fetch('/admin/products/update-enabled', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ ids, enabled: status })
        });
    }
}
