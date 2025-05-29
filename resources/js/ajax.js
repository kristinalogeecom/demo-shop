export class HttpClient {
    async get(url) {
        const response = await fetch(url);
        return await response.json();
    }

    async post(url, data) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
        });
        return await response.json();
    }

    simplePost(url) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
        });
    }

}
